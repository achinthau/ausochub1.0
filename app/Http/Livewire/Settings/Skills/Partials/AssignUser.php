<?php

namespace App\Http\Livewire\Settings\Skills\Partials;

use App\Models\Agent;
use App\Models\AgentSkill;
use App\Models\Extension;
use App\Models\Skill;
use App\Models\User;
use App\Repositories\ApiManager;
use Livewire\Component;

class AssignUser extends Component
{
    public $assignUserSkillModal = false;
    public $users;
    public $skills;
    public $user;
    public $skill;
    public $selectedSkills = [];

    protected $rules  = [

        'user' => 'required',
        'selectedSkills' => 'required',
    ];

    protected $validationAttributes = [
        'selectedSkills.*.skill' => 'skill',
        'selectedSkills.*.level' => 'level',
    ];

    protected $listeners = ['refreshData' => 'refreshData'];


    public function mount()
    {
        $this->refreshData();
    }

    public function render()
    {
        return view('livewire.settings.skills.partials.assign-user');
    }


    public function updatedUser($value)
    {
        $user = User::where('agent_id', $value)->first();
        if ($user->skills) {
            $this->selectedSkills = array_keys($user->skills->skill_ids);
        } else {
            $this->selectedSkills = [];
        }
        // dd($user->skills->skill_ids);
    }

    public function assign()
    {

        $this->validate();

        $skills = Skill::whereIn('skillid', $this->selectedSkills)->get();



        $agentSkill = AgentSkill::updateOrCreate(
            [
                'agentid' => $this->user
            ],
            [
                'skills' => implode(',', $skills->pluck('skillname')->toArray()),
                'skill_ids' => $skills->pluck('skillname', 'skillid')
            ]
        );


        $this->assignUserSkillModal = false;
        $this->resetForm();



        $this->emitTo('tables.settings.user-table', 'refreshLivewireDatatable');
    }

    public function resetForm()
    {

        $this->user = null;
        $this->selectedSkills = [];

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function addSkill()
    {
        array_push($this->selectedSkills, [
            'skill' => null,
            'level' => null,
        ]);
    }

    public function refreshData()
    {
        $this->users = User::hasExtension()->get();
        $this->skills = Skill::all();
    }
}
