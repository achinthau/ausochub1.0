<?php

namespace App\Http\Livewire\Settings\Users;

use App\Models\Agent;
use App\Models\Outlet;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Create extends Component
{
    public $createUserModal= false;
    public $userTypes ;
    public $outlets ;

    public User $user;

    protected $rules  = [
       
        'user.name' => 'required',
        'user.user_type_id' => 'required|exists:user_types,id',
        'user.email' => 'required|email|unique:users,email',
        'user.user_name'=>'required|unique:users,user_name',
        'user.phone' => 'nullable',
        'user.nic' => 'required',
        'user.gender' => 'required',
        'user.address' => 'nullable',
        'user.outlet_id' => 'required_if:user.user_type_id,5,6',
    
    ];

    protected $validationAttributes = [
        'user.user_type_id' => 'user type',
    ];

    protected $messages = [
        'user.outlet_id.required_if' => 'The outlet field is required.',
    ];
    

    public function mount()
    {
        $this->userTypes = UserType::select('id','title')->get()->toArray();
        $this->outlets = Outlet::select('id','title')->get()->toArray();

    }

    public function render()
    {
        return view('livewire.settings.users.create');
    }


    public function updatedCreateUserModal($value)
    {
       $this->resetForm();
    }

    public function save()
    {
        $this->validate();
        $this->user->password = Hash::make("auso123");
        $this->user->save();
        $this->createUserModal= false;
        
        $this->emitTo('tables.settings.user-table','refreshLivewireDatatable');
        $this->emitTo('settings.users.partials.assign-extension','refreshData');
    }

    public function resetForm()
    {
        $this->user = new User();
        
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
