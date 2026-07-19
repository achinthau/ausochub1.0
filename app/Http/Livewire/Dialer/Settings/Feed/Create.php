<?php

namespace App\Http\Livewire\Dialer\Settings\Feed;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Feed;
use Illuminate\Support\Facades\Auth;
use WireUi\Traits\Actions;
use App\Jobs\ProcessFeedFile;

class Create extends Component
{
    use WithFileUploads, Actions;

    public $feed = [];
    public $file;
    public $createFeedModal = false;

    protected $rules = [
        'feed.name' => 'required|string|max:255',
        'feed.description' => 'nullable|string',
        // 'file' => 'required|file|mimes:xlsx,csv,xls|max:204800', // 200MB
    ];

    public function render()
    {
        return view('livewire.dialer.settings.feed.create');
    }

    public function save()
    {
        $this->validate();

        if($this->file)
        {
            $originalName = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        $timestampedName = $originalName . '_' . now()->format('YmdHis') . '.' . $this->file->getClientOriginalExtension();

        $path = $this->file->storeAs('feeds', $timestampedName, 'public');


        }
        

        $feed = Feed::create([
            'name' => $this->feed['name'],
            'description' => $this->feed['description'] ?? null,
            // 'uploaded_by' => Auth::id(),
            // 'file_name' => $timestampedName,
            // 'status' => 'pending',
        ]);
        $feed->save();

//         Log::info("Feed created with ID: {$feed->id}");

        // Dispatch job to process the file
        ProcessFeedFile::dispatch(
            $feed->id,
            storage_path("app/public/feeds/{$feed->file_name}")
        )
            ->onConnection('database')
            ->onQueue('feeds');


        $this->emit('feedTableUpdated');
        $this->dispatchBrowserEvent('notify', ['message' => 'Feed uploaded. Processing will start shortly.']);
        $this->createFeedModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->feed = [];
        $this->file = null;
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
