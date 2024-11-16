<?php

namespace App\Http\Livewire\Admin\Dashboard;

use stdClass;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class Dashboard extends Component
{
    

    // public function mount()
    // {
    //     $request = request();
    //     $allUsers = new UserAuthController;
    //     $columnsResponse = $allUsers->index($request);
    //     $columnsData = json_decode($columnsResponse->content(), true);
    //     // dd($columnsData);
    // }



    public function render()
    {
        return view('livewire.admin.dashboard.dashboard');
    }
}
