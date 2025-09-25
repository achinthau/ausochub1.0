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
    public $calleeEmail;
    public $callerEmail;

    protected $listeners = ['show' => 'showDetailsModal'];
    public function render()
    {
        return view('livewire.reports.partials.cdr-details-modal');
    }

    public function showDetailsModal($src, $dst)
{
    // dd($src.$dst);

    $this->showCdrDetailsModal = true;

    $caller = $this->findName($src);
    $callee = $this->findName($dst);

    $this->callerName  = $caller['name'];
    $this->callerEmail = $caller['email'];

    $this->calleeName  = $callee['name'];
    $this->calleeEmail = $callee['email'];

    
}


    protected function findName($number)
{
    $number = trim($number);

    // if (!ctype_digit($number)) {
    //     return [
    //         'name'  => $number,  
    //         'email' => null,
    //         'type'  => 'system'
    //     ];
    // }
    if (!preg_match('/^\d+$/', $number)) {
        return [
            'name'  => $number,   // return raw value
            'email' => null,
            'type'  => 'system'
        ];
    }

    
    if (strlen($number) === 3) {
        // $user = User::whereHas('extensionDetails', function ($query) use ($number) {
        //     $query->where('extension', $number);
        // })->first();
        $user = User::where('extension', $number)->first();
        // dd($user);


        if ($user) {
            return [
                'name'  => $user->name,
                'email' => null,
                'type'  => 'user'
            ];
        }

        return [
            'name'  => "Ext {$number}",
            'email' => null,
            'type'  => 'user'
        ];
    }

  
    $lead = Lead::where('contact_number', $number)
                ->orWhere('contact_number_2', $number)
                ->first();

    if ($lead) {
        return [
            'name'  => $lead->first_name.' '.$lead->last_name,
            'email' => $lead->email,
            'type'  => 'lead'
        ];
    }

    return [
        'name'  => "Unknown ({$number})",
        'email' => null,
        'type'  => 'unknown'
    ];
}


}
