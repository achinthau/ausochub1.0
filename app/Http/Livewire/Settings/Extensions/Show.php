<?php

namespace App\Http\Livewire\Settings\Extensions;

use App\Models\Extension;
use App\Repositories\ApiManager;
use Livewire\Component;
use WireUi\Traits\Actions;

class Show extends Component
{
    use Actions;

    public Extension $extension;
    public $updateExtensionModal = false;

    protected $rules  = [

        'extension.extension' => 'required|numeric|digits_between:3,4',
        'extension.exten_type' => 'required',
        'extension.password' => 'required',
        'extension.context' => 'required',

    ];

    protected $validationAttributes = [
        'extension.exten_type' => 'extension type',
    ];

    protected $listeners = ['showUpdateExtensionModal' => 'showUpdateExtensionModal'];

    public function render()
    {
        return view('livewire.settings.extensions.show');
    }

    public function showUpdateExtensionModal($id)
    {
        $this->updateExtensionModal = true;
        $this->extension = Extension::find($id);
    }

    public function updatedUpdateExtensionModal($value)
    {
        if (!$value) {
            $this->resetForm();
        }
    }

    public function save()
    {
        $this->validate();
        $data = [
            ['name' => 'extension', 'contents' => $this->extension->extension,],
            ['name' => 'password', 'contents' => $this->extension->password,],
            ['name' => 'context', 'contents' => $this->extension->context,],
            ['name' => 'status', 'contents' => $this->extension->status,],
            ['name' => 'exten_type', 'contents' => $this->extension->exten_type,],
            ['name' => 'type', 'contents' => $this->extension->exten_type,],
            ['name' => 'updatedby', 'contents' => $this->extension->updatedby,],
            ['name' => 'id', 'contents' => $this->extension->id,],
        ];

        $response = ApiManager::updateExtension($data);

        if ($response) {
            $this->extension->save();
            $this->emitTo('tables.settings.extension-table', 'refreshLivewireDatatable');

            $this->updateExtensionModal = false;
            $this->resetForm();

            $this->notification()->success(
                $title = 'Success',
                $description = 'Extension successfull saved'
            );
        }else{
            $this->notification()->error(
                $title = 'Error',
                $description = 'Api Request Failed on Call Server'
            );
        }
        
    }

    public function resetForm()
    {
        $this->extension = new Extension();
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
