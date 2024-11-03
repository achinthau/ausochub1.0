<?php

namespace App\Http\Livewire\ContactFeeds;

use App\Models\ContactFeed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public ContactFeed $contactFeed;
    public $file;

    public $createContactFeedModal = false;

    protected $rules = [
        'contactFeed.title'=>'required',
        'contactFeed.description'=>'nullable',
        'file'=>'required|max:2048',
    ];

    public function render()
    {
        return view('livewire.contact-feeds.create');
    }

    public function updatedCreateContactFeedModal($value)
    {
        $this->contactFeed = new ContactFeed();

        if (!$value) {
            $this->resetErrorBag();
            $this->resetValidation();
        }
    }

    public function save()
    {
        $this->validate();
        $this->contactFeed->save();
        $this->file->storeAs('contact_feeds',"feed_".$this->contactFeed->id.".".$this->file->getClientOriginalExtension());
        $this->contactFeed->file_name=$this->contactFeed->id.".".$this->file->getClientOriginalExtension();
        $this->contactFeed->save();
        $this->createContactFeedModal = false;
        $this->emitTo('tables.contact-feeds-table','refreshLivewireDatatable');
    }
}
