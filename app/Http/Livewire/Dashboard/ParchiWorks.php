<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class Parchiworks extends Component
{

   
    public function render()
    {
        return view('livewire.dashboard.parchiWorks.parchiWorks');
    }
}
