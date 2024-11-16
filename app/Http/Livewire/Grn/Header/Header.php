<?php

namespace App\Http\Livewire\grn\Header;

use Livewire\Component;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class Header extends Component
{
    public $persistedActiveFeature = '';
    public $persistedTemplate = '';
    public $errorMessage;
    public $successMessage;
    protected $listeners = [
        'featureRoute' => 'handleFeatureRoute',
        'innerFeatureRoute' => 'handleFeatureRoute',
        'manualReceiverAdded' => 'handleManualReceiverAdded',
        'forceDelete' => 'deleteChallanSeries',
    ];

    protected $userAuthController;

    // public function mount(UserAuthController $userAuthController)
    // {
    //     $this->userAuthController = $userAuthController;

    //     $sessionId = session()->getId(); 
    //     $template = request('template', 'index');
    //     if (view()->exists('components.panel.grn.' . $template)) {
    //         $this->persistedTemplate = $template;
    //         $this->persistedActiveFeature = $template;
    //     }
    // }

    public function savePersistedTemplate($template, $activeFeature = null)
    {
        session(['persistedTemplate' => $template]);
        session(['persistedActiveFeature' => $activeFeature]);
    }

    // public function handleFeatureRoute($template, $activeFeature)
    // {
    //     $viewPath = 'components.panel.' . 'grn' . '.' . $template;
    //     // dd($viewPath);
    //     $this->persistedTemplate = view()->exists($viewPath) ? $template : 'index';
    //     // dd($this->persistedTemplate, $activeFeature);
    //     $this->persistedActiveFeature = view()->exists($viewPath) ? $activeFeature : null;
    //     $this->savePersistedTemplate($template, $activeFeature);

    //     // Redirect to the 'grn' route with the template as a query parameter
    //     return redirect()->route('grn', ['template' => $this->persistedTemplate]);   
    // }

    // public function featureRedirect($template, $activeFeature)
    // {
    //     // dd($template, $activeFeature);
    //     $this->emit('innerFeatureRoute', $template, $activeFeature);
    //     $this->template = '';
    //     $this->activeFeature = '';
    // }

    
    public function panelRedirect()
    {
        $this->savePersistedTemplate('index');
        return redirect()->route('grn');
    }
    
    public function Logout()
    {
        $request = request();
        $response = $this->userAuthController->user_logout($request)->getData();

        $this->successMessage = $response->success == "true" ? $response->message : null;
        $this->errorMessage = $response->success == "true" ? null : json_encode($response->error ?? [[$response->message]]);

        return redirect()->route('login');
    }

   
    public function render()
    {
        $request = request();
        $template = request('template', 'index');
        if($template == 'create-goods-receipt'){
            $template = 'create';
        }elseif($template == 'sent-goods-receipt'){
            $template = 'sent notes';
        }elseif($template == 'add-goods-receiver'){
            $template = 'Add Receivers';
        }elseif($template == 'all-goods-receiver'){
            $template = 'All Receivers';
        }elseif($template == 'goods-prefix'){
            $template = 'Prefix';
        }
        $this->persistedTemplate = $template;
        $this->persistedActiveFeature = $template;
        return view('livewire.header.header');
    }
}