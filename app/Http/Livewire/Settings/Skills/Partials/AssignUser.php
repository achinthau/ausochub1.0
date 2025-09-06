<?php

namespace App\Http\Livewire\Settings\Skills\Partials;

use App\Models\AgentSkill;
use App\Models\Skill;
use App\Models\User;
use Livewire\Component;

class AssignUser extends Component
{
    public $assignUserSkillModal = false;
    public $users;
    public $skills;
    public $user;
    public $type;
    public $selectedSkills = [];

    protected $rules  = [
        'user' => 'required',
        'type' => 'required|in:inbound,dialer',
        'selectedSkills' => 'required|array|min:1',
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
    if ($this->type) {
        $this->loadUserSkills($value, $this->type);
    } else {
        $this->selectedSkills = [];
    }
}


    public function updatedType($value)
{
    if ($this->user) {
        $this->loadUserSkills($this->user, $value);
    } else {
        $this->selectedSkills = [];
    }
}

    private function loadUserSkills($userId, $type)
{
    $agentSkill = AgentSkill::where('agentid', $userId)->first();

    if (!$agentSkill) {
        $this->selectedSkills = [];
        return;
    }

    if ($type === 'inbound' && !empty($agentSkill->skill_ids) && is_array($agentSkill->skill_ids)) {
        $this->skills = Skill::where('type','inbound')->get();
        $this->selectedSkills = array_keys($agentSkill->skill_ids);
    } elseif ($type === 'dialer' && !empty($agentSkill->dialer_skill_ids) && is_array($agentSkill->dialer_skill_ids)) {
        $this->skills = Skill::where('type','dialer')->get();
        $this->selectedSkills = array_keys($agentSkill->dialer_skill_ids);
    } else {
        $this->selectedSkills = [];
    }
}



    public function assign()
{
    $this->validate();

    $skills = Skill::whereIn('skillid', $this->selectedSkills)->get();

    
    $agentSkill = AgentSkill::firstOrCreate(
        ['agentid' => $this->user],
        ['skills' => ''] // default
    );

    if ($this->type === 'inbound') {
        $agentSkill->skill_ids = $skills->pluck('skillname', 'skillid');
    } elseif ($this->type === 'dialer') {
        $agentSkill->dialer_skill_ids = $skills->pluck('skillname', 'skillid');
    }

    
    $agentSkill->skills = implode(',', $skills->pluck('skillname')->toArray());

    $agentSkill->save();

    $this->assignUserSkillModal = false;
    $this->resetForm();

    $this->emitTo('tables.settings.user-table', 'refreshLivewireDatatable');
}


    public function resetForm()
    {
        $this->user = null;
        $this->type = null;
        $this->selectedSkills = [];
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function refreshData()
    {
        $this->users = User::hasExtension()->get();
        $this->skills = Skill::all();
    }
}
