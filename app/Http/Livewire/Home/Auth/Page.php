<?php

namespace App\Http\Livewire\Home\Auth;

use App\Models\UserQuery;
use App\Models\Feature;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Page\PageController;

class Page extends Component
{
    public $getData, $receiverData;
    public $userQueryData = [
        'phone' => '',
        'email'  => '',
        'comment' => '',
    ];
    
    public $password;
    public $isModalOpen = false;

    public $isAuthenticated;
    public $persistedTemplate;
    public $successMessage;
    public $errorMessage;
    public $otp = '';
    public $status;
    protected $response = [];

    public function mount(){
        $request = request();
        $slug = request()->route('slug');
        $userProfile = new PageController;
        $response = $userProfile->show($request, $slug);

        $responseData = (array) $response->getData();

        // Assign the array to the receiverData property
        $this->receiverData = $responseData;
    }
 
    public function render()
    {
        // $request = request();
        // $slug = request()->route('slug');
        // $userProfile = new PageController;
        // $response = $userProfile->show($request, $slug);

        // $this->receiverData = $response->getData();
        // // dump($receiverData);
        // $this->receiverDatas = $receiverData->data;
        // $this->pageData = json_decode($data->getContent(), true);
        return view('livewire.home.auth.page')
        ->extends('layouts.home.app')->section('body');
    }
}
