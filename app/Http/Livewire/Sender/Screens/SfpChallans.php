<?php

namespace App\Http\Livewire\Sender\Screens;

use Livewire\Component;

class SfpChallans extends Component
{
    public $activeTab;
    public $status = 'active';
    protected $queryString = ['activeTab'];


    public function mount()
    {
        if (!request()->query('activeTab')) {
            $this->activeTab = 'tab1';
        }
    }

    public function render()
    { 
        return view('livewire.sender.screens.sfp-challans');
    }
}
