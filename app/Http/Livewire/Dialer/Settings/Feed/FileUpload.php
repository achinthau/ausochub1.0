<?php

namespace App\Http\Livewire\Dialer\Settings\Feed;

use App\Jobs\ProcessFeedFile;
use App\Models\Feed;
use App\Models\FeedContact;
use App\Models\FeedContactInValid;
use App\Models\FeedContactValid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\Actions;
use Maatwebsite\Excel\Facades\Excel;

use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Row;

class FileUpload extends Component
{
    use WithFileUploads, Actions;

    public $FeedUploadModal = false;
    public $feedId = '';
    public $name = '';
    public $description = '';
    public $file;

    protected $listeners = ['showFeedUploadModal' => 'showFeedUploadModal'];

    public function showFeedUploadModal($id)
    {
        // dd($id);
        $this->FeedUploadModal = true;
        $this->feedId = $id;

        $feed = Feed::find($id);
        if ($feed) {
            $this->name = $feed->name;
            $this->description = $feed->description;
        }
    }

    public function save()
    {
        $originalName = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        $timestampedName = $originalName . '_' . now()->format('YmdHis') . '.' . $this->file->getClientOriginalExtension();

        $path = $this->file->storeAs('feeds', $timestampedName, 'public');

        $feed = Feed::find($this->feedId);
        if ($feed) {
            $feed->update([
                'name' => $this->name,
                'description' => $this->description,
                'file_name' => $timestampedName,
                'status' => 'pending',
                'uploaded_by' => Auth::id(),
            ]);
        }


        $filePath = storage_path("app/public/feeds/{$feed->file_name}");
ProcessFeedFile::dispatch($feed->id, $filePath)
    ->onConnection('database')
    ->onQueue('feeds');




        $filePath   = storage_path("app/public/feeds/{$feed->file_name}");
    $batchSize  = 5000;

    $feed->update(['status' => 'processing']);
    Log::info("Processing Feed ID: {$feed->id}");

    // Import Excel file and insert into FeedContact
    Excel::import(new class($feed->id, $batchSize) implements OnEachRow, WithChunkReading {
        protected int $feedId;
        protected int $batchSize;
        protected array $buffer = [];
        static ?array $header = null;

        public function __construct(int $feedId, int $batchSize)
        {
            $this->feedId = $feedId;
            $this->batchSize = $batchSize;
        }

        public function onRow(Row $row)
        {
            $rowArray = $row->toArray();

            // First row = header
            if (!self::$header) {
                self::$header = array_map(fn($h) => strtolower(trim($h)), $rowArray);
                Log::info("Excel headers detected: " . implode(", ", self::$header));
                return;
            }

            if (empty(array_filter($rowArray))) return;

            $rowData = array_combine(self::$header, $rowArray);
            if (!$rowData) {
                Log::warning("Failed to combine row with header: " . json_encode($rowArray));
                return;
            }

            $phone = $rowData['phone'] ?? $rowData['contact'] ?? null;
            // $phone = is_string($phone) ? preg_replace('/\D/', '', $phone) : $phone;

            $this->buffer[] = [
                'feed_id'       => $this->feedId,
                'phone'         => $phone,
                'customer_name' => $rowData['customer name'] ?? null,
                'data'          => json_encode($rowData),
                'created_at'    => now(),
                'updated_at'    => now(),
            ];

            if (count($this->buffer) >= $this->batchSize) {
                FeedContact::insert($this->buffer);
                Log::info(count($this->buffer) . " contacts inserted into FeedContact.");
                $this->buffer = [];
            }
        }

        public function chunkSize(): int
        {
            return $this->batchSize;
        }

        public function __destruct()
        {
            if (!empty($this->buffer)) {
                FeedContact::insert($this->buffer);
                Log::info(count($this->buffer) . " contacts inserted into FeedContact (final batch).");
                $this->buffer = [];
            }
        }
    }, $filePath);

    // Validate contacts


    ProcessFeedFile::dispatch($feed->id)
    ->onConnection('database')
    ->onQueue('feeds');


        $this->FeedUploadModal = false;
        $this->emit('feedUpdated');
    }

    public function render()
    {
        return view('livewire.dialer.settings.feed.file-upload');
    }
}
