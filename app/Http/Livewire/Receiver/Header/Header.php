<?php

namespace App\Http\Livewire\Receiver\Header;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use Livewire\Component;

class Header extends Component
{
    public $successMessage, $errorMessage;
    public $persistedActiveFeature = '';
    public $persistedTemplate = '';
    public $template;
    public $activeFeature;
    protected $userAuthController;
    protected $listeners = [
        'featureRoute' => 'handleFeatureRoute',
        'innerFeatureRoute' => 'handleFeatureRoute',
        'manualReceiverAdded' => 'handleManualReceiverAdded',
        'forceDelete' => 'deleteChallanSeries',
    ];
    public function savePersistedTemplate($template, $activeFeature = null)
    {
        session(['persistedTemplate' => $template]);
        session(['persistedActiveFeature' => $activeFeature]);
    }
    public function handleFeatureRoute($template, $activeFeature)
    {
        $viewPath = 'components.panel.' . 'receiver' . '.' . $template;
        // dd($viewPath);
        $this->persistedTemplate = view()->exists($viewPath) ? $template : 'index';
        // dd($this->persistedTemplate, $activeFeature);
        $this->persistedActiveFeature = view()->exists($viewPath) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);

        // Redirect to the 'receiver' route with the template as a query parameter
        return redirect()->route('receiver', ['template' => $this->persistedTemplate]);   
    }
    public function mount(UserAuthController $userAuthController)
    {
        $this->userAuthController = $userAuthController;

        $sessionId = session()->getId(); 
        $template = request('template', 'index');
        if (view()->exists('components.panel.receiver.' . $template)) {
            $this->persistedTemplate = $template;
            $this->persistedActiveFeature = $template;
        }
    }
    public function featureRedirect($template, $activeFeature)
    {
        $this->emit('featureRoute', $template, $activeFeature);
        $this->template = '';
        $this->activeFeature = '';
    }
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
    public function panelRedirect(){
        session()->forget('persistedTemplate');
        return redirect()->route('receiver');
    }
    
    public function render()
    {
        return view('livewire.header.header');
    }
}
