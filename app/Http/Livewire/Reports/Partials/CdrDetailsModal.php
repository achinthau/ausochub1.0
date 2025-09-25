<?php

namespace App\Http\Livewire\Reports\Partials;

use App\Models\Cdr;
use App\Models\Lead;
use App\Models\User;
use Livewire\Component;

class CdrDetailsModal extends Component
{
    public $showCdrDetailsModal = false;
    public $callerName;
    public $calleeName;
    public $calleeNic;
    public $calleeAddress;
    public $calleeWhatsapp;
    public $calleeEmail;
    public $callerEmail;
    public $callerAddress;
    public $callerWhatsapp;
    public $callerNic;

    protected $listeners = ['show' => 'showDetailsModal'];
    public function render()
    {
        return view('livewire.reports.partials.cdr-details-modal');
    }

    public function showDetailsModal($src, $dst,$direction,$extension)
{
    // dd($src.$dst);

    $this->showCdrDetailsModal = true;

    // dd($direction);
    if($direction == 'Dial')
    {        
        // dd($extension);
        $caller = $this->findName($extension);
        // dd($caller);
    } else
    {
        $caller = $this->findName($src);
    }
    $callee = $this->findName($dst);

    $this->callerName  = $caller['name'];
    $this->callerEmail = $caller['email'];
    $this->callerAddress = $caller['address'];
    $this->callerWhatsapp = $caller['whatsapp'];
    $this->callerENicl = $caller['nic'];

    $this->calleeName  = $callee['name'];
    $this->calleeEmail = $callee['email'];
    $this->calleeAddress = $callee['address'];
    $this->calleeWhatsapp = $callee['whatsapp'];
    $this->calleeNic = $callee['nic'];

    
}


    protected function findName($number)
{
    $number = trim($number);

    
    if (!preg_match('/^\d+$/', $number)) {
        return [
            'name'  => $number,
            'email' => null,
            'type'  => 'system',
            'address' => null,
            'whatsapp' => null,
            'nic' => null,
        ];
    }

    
    if (strlen($number) === 3) {
        $user = User::where('extension', $number)->first();

        if ($user) {
            return [
                'name'     => $user->name,
                'email'    =>  null,
                'type'     => 'user',
                'address'  => null,
                'whatsapp' => null,
                'nic'      => null,
            ];
        }

        return [
            'name'     => "Ext {$number}",
            'email'    => null,
            'type'     => 'user',
            'address'  => null,
            'whatsapp' => null,
            'nic'      => null,
        ];
    }

    
    $lead = Lead::where('contact_number', $number)
                ->orWhere('contact_number_2', $number)
                ->first();

    if ($lead) {
        return [
            'name'     => trim($lead->first_name.' '.$lead->last_name),
            'email'    => $lead->email,
            'type'     => 'lead',
            'address'  => $lead->address_line_1.' '.$lead->address_line_2.' '.$lead->city,
            'whatsapp' => $lead->whatsapp,
            'nic'      => $lead->nic,
        ];
    }

    
    return [
        'name'     => "Unknown ({$number})",
        'email'    => null,
        'type'     => 'unknown',
        'address'  => null,
        'whatsapp' => null,
        'nic'      => null,
    ];
}



}
