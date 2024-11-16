<?php

namespace App\Http\Livewire\Dashboard\Header;

use Livewire\Component;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class Header extends Component
{
    public $successMessage;
    public $errorMessage, $open;
    protected $listeners = ['cartUpdated' => '$refresh'];
    
    public function Logout()
    {
        // Validate the input data
        $request = request();
        // $validation = $this->validate();

        // Inject the UserAuthController class as a dependency
        $userAuthController = new UserAuthController;

        // Login the user and get the response
        $response = $userAuthController->user_logout($request);
        $response = $response->getData();
        // dd($response);
        if ($response->success == "true") {
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);
            // dd($response);
            return redirect()->route('login');
        } else {
            // dd($response);
            $this->errorMessage = json_encode($response->error ?? [[$response->message]]);
            return redirect()->route('login');

        }
        // Return success response with user details and token
    }
    

    public function panelRedirect()
    {
        dd('panelRedirect');
    }

    public function render()
    {
        // dd('header');
        return view('livewire.header.header');
    }
}
