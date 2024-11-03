<?php

namespace App\Http\Livewire\Settings\Extensions;

use App\Models\Extension;
use App\Repositories\ApiManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;

    public Extension $extension;

    public $createExtensionModal = false;

    protected $rules  = [

        'extension.extension' => 'required|numeric|digits_between:3,4|unique:mysql-old.au_exten,extension',
        'extension.exten_type' => 'required',
        'extension.password' => 'required',
        'extension.context' => 'required',

    ];

    protected $validationAttributes = [
        'extension.exten_type' => 'extension type',
    ];

    public function render()
    {
        return view('livewire.settings.extensions.create');
    }

    public function updatedCreateExtensionModal($value)
    {
        $this->resetForm();
    }

    public function save()
    {


        $this->validate();
        $this->extension->status = 1;
        $this->extension->updatedby = Auth::user()->name;
        $this->extension->datetime = Carbon::now();
        // 

        $data = [
            ['name' => 'extension', 'contents' => $this->extension->extension,],
            ['name' => 'password', 'contents' => $this->extension->password,],
            ['name' => 'context', 'contents' => $this->extension->context,],
            ['name' => 'status', 'contents' => $this->extension->status,],
            ['name' => 'exten_type', 'contents' => $this->extension->exten_type,],
            ['name' => 'type', 'contents' => $this->extension->exten_type,],
            ['name' => 'updatedby', 'contents' => $this->extension->updatedby,],
        ];

        $response = ApiManager::createExtension($data);

        if ($response) {
            //$this->extension->save();
            $this->emitTo('tables.settings.extension-table', 'refreshLivewireDatatable');

            $this->createExtensionModal = false;
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
