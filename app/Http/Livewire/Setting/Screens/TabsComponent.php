<?php

namespace App\Http\Livewire\Setting\Screens;

use Livewire\Component;

class TabsComponent extends Component
{
    public $activeTab = 'panel-1';

    public function changeTab($panelId)
    {
        $this->activeTab = 'panel-' . $panelId;
        // Additional logic if needed
        $this->emit('tabChanged', $this->activeTab);
    }
    public function getContent()
    {
        // dd($this->activeTab);
        if ($this->activeTab === 'panel-1') {
            return view('user.setting.profile');
        } elseif ($this->activeTab === 'panel-2') {
            return view('livewire.setting.screens.address');
        }

        // Add more conditions for other tabs if needed
    }
    // public function render()
    // {
    //     return view('livewire.setting.screens.tabs-component');
    // }
    public function render()
    {
        return view('livewire.setting.screens.tabs-component', [
            'content' => $this->getContent(),
        ]);
    }
}
