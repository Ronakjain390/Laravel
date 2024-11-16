<?php

namespace App\Http\Livewire\Seller\Header;
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
        'manualsellerAdded' => 'handleManualsellerAdded',
        'forceDelete' => 'deleteChallanSeries',
    ];
    public function mount(UserAuthController $userAuthController)
    {
        $this->userAuthController = $userAuthController;

        $sessionId = session()->getId(); 
        $template = request('template', 'index');
        if (view()->exists('components.panel.seller.' . $template)) {
            $this->persistedTemplate = $template;
            $this->persistedActiveFeature = $template;
        }
    }
    public function savePersistedTemplate($template, $activeFeature = null)
    {
        session(['persistedTemplate' => $template]);
        session(['persistedActiveFeature' => $activeFeature]);
    }
    public function handleFeatureRoute($template, $activeFeature)
    {
        $viewPath = 'components.panel.' . 'seller' . '.' . $template;
        // dd($viewPath);
        $this->persistedTemplate = view()->exists($viewPath) ? $template : 'index';
        // dd($this->persistedTemplate, $activeFeature);
        $this->persistedActiveFeature = view()->exists($viewPath) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);

        // Redirect to the 'seller' route with the template as a query parameter
        return redirect()->route('seller', ['template' => $this->persistedTemplate]);   
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
        return redirect()->route('seller');
    }
    public function render()
    {
        $request = request();
        $template = request('template', 'index');
        if($template == 'purchase_order_seller'){
            $template = 'purchase_order';
        }elseif($template == 'invoice_series_no'){
            $template = 'invoice prefix';
        }
        $this->persistedTemplate = $template;
        $this->persistedActiveFeature = $template;
        return view('livewire.header.header');
    }
}
