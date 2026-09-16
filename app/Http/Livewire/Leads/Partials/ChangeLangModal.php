<?php

namespace App\Http\Livewire\Leads\Partials;

use App\Models\FeedContactValid;
use App\Models\Language;
use Livewire\Component;

class ChangeLangModal extends Component
{
    public $ChangeLangModal = false;
    public $lang = '';
    public $langOptions = [];
    public $feedContactIds = [];

    protected $listeners = ['openChangeLangModal' => 'openModal'];

    public function openModal($feedContactIds = [])
    {
        $this->feedContactIds = is_array($feedContactIds)
            ? array_values(array_filter($feedContactIds))
            : array_values(array_filter(array_map('trim', explode(',', (string) $feedContactIds))));

        $this->langOptions = Language::orderBy('id')->pluck('name')->values()->toArray();

        $firstId = $this->feedContactIds[0] ?? null;
        $firstLang = $firstId ? FeedContactValid::where('id', $firstId)->value('lang') : null;

        $this->lang = $this->matchLangOption($firstLang) ?? ($this->langOptions[0] ?? '');

        $this->ChangeLangModal = true;
    }

    public function updateLang()
    {
        if (empty($this->lang)) {
            $this->addError('lang', 'Please select a language.');
            return;
        }

        if (!empty($this->feedContactIds)) {
            FeedContactValid::whereIn('id', $this->feedContactIds)
                ->update([
                    'lang' => $this->lang,
                    'assigned_to' => null,
                ]);
        }

        $this->ChangeLangModal = false;
        $this->dispatchBrowserEvent('reload-page');
    }

    protected function matchLangOption($lang)
    {
        if (!$lang) {
            return null;
        }

        foreach ($this->langOptions as $option) {
            if (strcasecmp((string) $lang, (string) $option) === 0) {
                return $option;
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.leads.partials.change-lang-modal');
    }
}