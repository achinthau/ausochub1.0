<?php

namespace App\Http\Livewire\Settings\Moh;

use App\Models\Agent;
use App\Models\MohClass;
use App\Models\User;
use App\Models\UserType;
use App\Repositories\ApiManager;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;
    public $createMhoClassModal= false;
    public MohClass $mohClass;

    protected $rules  = [
        'mohClass.moh_class' => 'required|alpha_dash|unique:mysql-old.au_moh_class,moh_class',
    ];

    protected $validationAttributes = [
        'mohClass.moh_class' => 'moh name',
    ];

    

    public function render()
    {
        return view('livewire.settings.moh.create');
    }


    public function updatedCreateMhoClassModal($value)
    {
       $this->resetForm();
    }

    public function save()
    {
        $this->validate();
       
        $data = [
            ['name' => 'mohName', 'contents' => $this->mohClass->moh_class]
        ];
        
        $response = ApiManager::createMohName($data);
        $this->createMhoClassModal= false;
        $this->notification()->success(
            $title = 'Success',
            $description = 'MOH Class successfully saved'
        );
        
        $this->emitTo('tables.settings.moh-class-table','refreshLivewireDatatable');
    }

    public function resetForm()
    {
        $this->mohClass = new MohClass();
        $this->resetErrorBag();
        $this->resetValidation();
    }

    
}
