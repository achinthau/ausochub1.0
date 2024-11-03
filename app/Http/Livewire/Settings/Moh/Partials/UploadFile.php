<?php

namespace App\Http\Livewire\Settings\Moh\Partials;

use App\Models\MohClass;
use App\Repositories\ApiManager;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\Process\Process;
use WireUi\Traits\Actions;

class UploadFile extends Component
{
    public $uploadFileModal = false;
    public MohClass $mohClass;
    public $mohFile;

    use WithFileUploads;
    use Actions;

    protected $listeners = ['showUploadFileModal' => 'showUploadFileModal'];

    public function render()
    {
        return view('livewire.settings.moh.partials.upload-file');
    }


    public function showUploadFileModal($id)
    {
        $this->mohClass = MohClass::find($id);
        $this->uploadFileModal=true;
    }

    public function save()
    {
        $this->validate([
            'mohFile' => 'required|mimes:wav|max:3072', // 1MB Max
        ]);

        $classname = $this->mohClass->moh_class;
        // dd($this->mohFile->getClientOriginalName());
        $fileName = $this->mohFile->getClientOriginalName();
        $this->mohFile->storeAs("SoundMOH/moh_$classname", $this->mohFile->getClientOriginalName(),'moh-file-server');
        $filePath = env('MOH_FILE_LOCATION');
        // dump("scp ".$filePath."SoundMOH/moh_$classname/$fileName root@143.244.146.27:/var/www/html/ausoadmin/SoundMOH/moh_$classname/$fileName");
        // exec("scp ".$filePath."SoundMOH/moh_$classname/$fileName root@143.244.146.27:/var/www/html/ausoadmin/SoundMOH/moh_$classname/$fileName");
        $data = [
            [
                'name' => 'mohClass',
                'contents' => $classname
            ]
        ];
        ApiManager::uploadMohFile($data);
        $this->uploadFileModal=false;
        $this->resetForm();

        $this->notification()->success(
            $title = 'Success',
            $description = 'MOH File successfully saved'
        );
        $this->emitTo('tables.settings.moh-class-table','refreshLivewireDatatable');
    }

    public function resetForm()
    {
        $this->mohClass = new MohClass();
        $this->mohFile = null;
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
