<?php

namespace App\Http\Livewire\Home;

use App\Models\User;
use App\Models\Feature;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class Home extends Component
{
    public $emailOrPhone;
    public $password;

    public $user;
    public $isAuthenticated;
    public $persistedTemplate;
    public $successMessage;
    public $errorMessage;
    public $otp = '';
    protected $response = [];

    // protected $rules = [
    //     'emailOrPhone' => 'required|exists:users,email',
    //     'password' => 'required|min:8',
    //     // 'otp' => 'required',
    // ];

    // public function updated($propertyEmailOrPhone)
    // {
    //     $this->validateOnly($propertyEmailOrPhone, $this->rules);
    // }

    // public function mount()
    // {
    //     if (session()->has('persistedTemplate')) {
    //         $this->persistedTemplate = view()->exists('components.livewire.auth.' . session('persistedTemplate')) ? session('persistedTemplate') : 'login';
    //     }
    // }

    public function userLogin()
    {
        // Validate the input data
        $request = request();
        // $validation = $this->validate();

        $request->merge([
            'email_or_phone' => $this->emailOrPhone,
            'password' =>  $this->password,
        ]);

        // Inject the UserAuthController class as a dependency
        $userAuthController = new UserAuthController;

        // Login the user and get the response
        $response = $userAuthController->login($request);
        $response = $response->getData();

        if ($response->success === "true") {
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);
            return redirect()->route('dashboard');
        } else {
            // dd($response);
            $this->errorMessage = json_encode($response->error??[$response->message]);
        }
        // Return success response with user details and token
    }

    public function userRegister(Request $request)
    {
        // Validate the input data
        $this->validate();
        $request->merge([
            'otp' => $this->otp,
        ]);

        // Register the user and get the response
        $userAuthController = app(UserAuthController::class);
        $response = $userAuthController->register($request);

        // Return success response with user details and token
        $this->response = $response->getData();
        session()->flash('success', 'You have been successfully registered!');
        return redirect()->route('dashboard');
    }
    public function render()
    {
        return view('livewire.home.home');
    }
}
