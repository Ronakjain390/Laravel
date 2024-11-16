<?php

namespace App\Http\Livewire\Receiver\Content;

use Livewire\Component;

class SfpReturnChallans extends Component
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
        return view('livewire.receiver.content.sfp-return-challans');
    }
}
