<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use Illuminate\Support\Facades\Auth;
use App\Mail\BookDemo;
use Illuminate\Support\Facades\Mail;

class Help extends Component
{
    public $showModal = false;

    public function bookDemo(Request $request){

        $userData = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user();
        // dd($userData);
        try {
            // dd('rty');
            // Mail::to("contact@theparchi.com")->send(new BookDemo($userData));
            Mail::to("contact@theparchi.com")->send(new BookDemo($userData));
            // dd('dent');
        } catch (\Throwable $exception) {
            // Log the exception
            // dd('mot');
            Log::channel('emaillog')->error($exception->getMessage());

            //     // You can also handle the exception in other ways, such as sending a notification or taking appropriate action
        }
        $this->showModal = true;
        // session()->flash('success', 'Demo has been Booked. We will get in touch with you Shortly');
    }
   
    public function render()
    {
        return view('livewire.dashboard.help.help');
    }
}
