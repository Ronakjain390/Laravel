<?php

namespace App\Http\Livewire\Dashboard\Sidebar;

use Livewire\Component;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class SettingSidebar extends Component
{

    public function render()
    {
        return view('livewire.side-bar.settingSidebar');
    }
}
