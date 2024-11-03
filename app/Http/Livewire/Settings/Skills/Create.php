<?php

namespace App\Http\Livewire\Settings\Skills;

use App\Models\MohClass;
use App\Models\Skill;
use App\Repositories\ApiManager;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;
    public $createSkillModal = false;
    public Skill $skill;
    public $mohClasses;

    protected $rules  = [
        'skill.skillname' => 'required|alpha_dash|unique:mysql-old.au_skills,skillname',
        'skill.mohClass' => 'required',
    ];

    protected $validationAttributes = [
        'skill.skillname' => 'skill name',
        'skill.mohClass' => 'MOH class',
    ];

    public function mount()
    {
        $this->mohClasses = MohClass::all();
    }

    public function render()
    {
        return view('livewire.settings.skills.create');
    }


    public function updatedCreateSkillModal($value)
    {
        $this->skill = new Skill;
        if ($value) {
        } else {
            $this->resetForm();
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            ['name' => 'queueName', 'contents' => $this->skill->skillname],
            ['name' => 'mohClass', 'contents' => $this->skill->mohClass]
        ];
        
        $response = ApiManager::createSkill($data);
        $this->createSkillModal= false;
        $this->notification()->success(
            $title = 'Success',
            $description = 'Skill successfully saved'
        );
        
        $this->emitTo('tables.settings.skill-table','refreshLivewireDatatable'); 
        $this->emitTo('settings.skills.partials.assign-user','refreshData'); 
    }

    public function resetForm()
    {
        $this->skill = new Skill();
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
