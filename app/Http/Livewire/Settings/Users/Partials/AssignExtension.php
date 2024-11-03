<?php

namespace App\Http\Livewire\Settings\Users\Partials;

use App\Models\Agent;
use App\Models\Extension;
use App\Models\User;
use App\Repositories\ApiManager;
use Livewire\Component;

class AssignExtension extends Component
{
    public $assignUserExtensionModal = false;
    public $users;
    public $extensions;
    public $user;
    public $extension;

    protected $rules  = [

        'user' => 'required',
        'extension' => 'required'
    ];

    protected $listeners = ['refreshData' => 'refreshData'];

    public function mount()
    {
        $this->refreshData();
    }

    public function render()
    {
        return view('livewire.settings.users.partials.assign-extension');
    }

    public function assign()
    {

        $this->validate();

        $user = Agent::find($this->user);
        $name = explode(' ', $user->name);

        $ext = Extension::where('extension',$this->extension)->first();

        $data = [
            ['name' => 'user', 'contents' => $name[0]],
            ['name' => 'extension', 'contents' => $this->extension],
            ['name' => 'exten_type', 'contents' => $ext->exten_type],
            ['name' => 'context', 'contents' => $ext->context],
        ];

        $response = ApiManager::assignExtension($data);



        $user->extension = $this->extension;
        $user->save();

        if ($user->user) {
            $user->user->extension=$this->extension;
            $user->user->save();
        }


        $this->assignUserExtensionModal = false;
        $this->resetForm();

        $this->refreshData();

        $this->emitTo('tables.settings.user-table', 'refreshLivewireDatatable');
    }

    public function refreshData()
    {
        $this->users = User::noAssigned()->get();
        $this->extensions = Extension::notAssigned()->get();
    }

    public function resetForm()
    {

        $this->user = null;
        $this->extension = null;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
