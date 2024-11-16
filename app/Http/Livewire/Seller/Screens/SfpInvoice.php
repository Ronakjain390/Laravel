<?php

namespace App\Http\Livewire\Seller\Screens;

use Livewire\Component;

class SfpInvoice extends Component
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
        return view('livewire.seller.screens.sfp-invoice');
    }
}
