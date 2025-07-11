<?php

namespace App\Http\Livewire\Settings\Users;

use App\Models\Agent;
use App\Models\CrmDepartment;
use App\Models\Extension;
use App\Models\User;
use App\Models\UserType;
use App\Repositories\ApiManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{

    public $updateUserModal = false;
    public $userTypes;

    public User $user;
    public $agent;
    public $extensions;

    public $departments;

    protected $rules  = [

        'user.name' => 'required',
        'user.user_type_id' => 'required|exists:user_types,id',
        'user.email' => 'required|email|unique:users,email',
        'user.user_name'=>'required|unique:users,user_name',
        'user.phone' => 'nullable',
        'user.nic' => 'nullable',
        'user.gender' => 'nullable',
        'user.address' => 'nullable',
        'user.tenant_context' => 'nullable',
        'user.extension' => 'nullable',
        'user.department_id' => 'nullable|exists:crm_departments,id',

    ];

    protected $validationAttributes = [
        'user.user_type_id' => 'user type',
    ];

    protected $listeners = ['show' => 'showUpdateUserModal', 'delete' => 'deleteUser'];

    public function mount()
    {
        $this->userTypes = UserType::select('id', 'title')->get()->toArray();
        $this->extensions = [];

        $this->departments = CrmDepartment::select('id','name')->get()->toArray();
    }
    public function render()
    {
        return view('livewire.settings.users.show');
    }


    public function showUpdateUserModal($id)
    {
        $this->departments = CrmDepartment::select('id','name')->get()->toArray();
        $this->user = User::find($id);
        $this->extensions = Extension::notAssigned($this->user->agent_id)->get();
        $this->updateUserModal = true;
    }

    public function save()
    {
        $this->rules['user.email'] =  $this->rules['user.email'] .','.$this->user->id;
        $this->rules['user.user_name'] =  $this->rules['user.user_name'] .','.$this->user->id;
        $this->validate();
        $this->user->save();
        
        if ($this->user->user_type_id > 2) {
            // $this->rules['user.extension'] =  'required';
            $this->validate();
            $agent = Agent::updateOrCreate(
                [
                    'email' => $this->user->email
                ],
                [
                    'username' => $this->user->user_name,
                    'password' => $this->user->email,
                    'fname' => $this->user->name,
                    'lname' => $this->user->name,
                    'usertype' => $this->user->userType->title,
                    'DOB' => Carbon::now(),
                    'NIC' => $this->user->nic,
                    'gender' => $this->user->gender,
                    'email' => $this->user->email,
                    'addressNo' => $this->user->address,
                    'extension' => $this->user->extension,
                    'numberone' => $this->user->phone,
                    'createdat' => Carbon::now(),
                    'updatedat' => Carbon::now(),
                    'updatedby' => Auth::id(),
                    'status' => 1
                ]
            );
            
            $this->user->agent_id = $agent->id;
            $this->user->save();
            $name = explode(' ',$this->user->name);


            if($agent->extension)    //added this to remove extension required rule
            {
            
            $data = [
                ['name' => 'user', 'contents' => $name[0]],
                // ['name' => 'extension', 'contents' => $agent->extension],
                ['name' => 'extension', 'contents' => $agent->extension ?? null],
                ['name' => 'exten_type', 'contents' => $agent->extensionDetails->exten_type ?? null],
                ['name' => 'context', 'contents' => $agent->extensionDetails->context ?? null],
            ];
            
            $response = ApiManager::assignExtension($data);
        }
        }       


        $this->updateUserModal = false;
        $this->emitTo('tables.settings.user-table','refreshLivewireDatatable');

    }

    // public function deleteUser($id)
    //     {
    //         $user = User::find($id);
            

    //         if ($user) {
    //             $user->delete();
    //             $agent = $user->agent;
    //             if ($agent) {
    //             $agent->delete();
    //         }
    //         }

    //         $this->emitTo('tables.settings.user-table','refreshLivewireDatatable');
    //     }

//     public function deleteUser($id)
// {
//     $user = User::with('agentLogins')->find($id); 
//     if (!$user) {
//         return session()->flash('error', 'User not found.');
//     }

//     $dateLimit = Carbon::now()->subDays(config('app.datecounts'));

//     $lastLogin = optional($user->agentLogins()->latest('login_time')->first())->login_time;

//     if ($lastLogin && $lastLogin > $dateLimit) {
//         return session()->flash('error', 'Cannot delete user who logged in within the last ' . config('app.datecounts') . ' days.');
//     }

//     $user->delete();

//     if ($user->agent) {
//         $user->agent->delete();
//     }

//     $this->emitTo('tables.settings.user-table','refreshLivewireDatatable');
//     return session()->flash('success', 'User deleted successfully.');
    
// }

public function deleteUser($id)
{
    $user = User::with('agentLogins')->find($id); 
    if (!$user) {
        $this->dispatchBrowserEvent('notification', [
            'type' => 'error',
            'message' => 'User not found.'
        ]);
        return;
    }

    $dateLimit = Carbon::now()->subDays(config('app.user_inactive_days'));

    $lastLogin = optional($user->agentLogins()->latest('login_time')->first())->login_time;

    if ($lastLogin && $lastLogin > $dateLimit) {
        $this->dispatchBrowserEvent('notification', [
            'type' => 'error',
            'message' => 'Cannot delete user who logged in within the last ' . config('app.user_inactive_days') . ' days.'
        ]);
        return;
    }

    $user->extension = null;
    $user->save();
    $user->delete();

    if ($user->agent) {
        $user->agent->extension = null;
    $user->agent->save();
        $user->agent->delete();
    }

    $this->emitTo('tables.settings.user-table','refreshLivewireDatatable');
    $this->emitTo('settings.users.partials.assign-extension','refreshData');

    $this->dispatchBrowserEvent('notification', [
        'type' => 'success',
        'message' => 'User deleted successfully.'
    ]);
}


    public function resetForm()
    {
        $this->user = new User();
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
