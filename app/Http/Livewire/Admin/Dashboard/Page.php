<?php

namespace App\Http\Livewire\Admin\Dashboard;

use stdClass;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\Page\PageController;
 


class Page extends Component
{
    public $statusCode, $title, $content, $successMessage, $status;
    public $pageData = array(
        'title' => '',
        'content' => '',
        'slug' => '',
    );
    
  

//    public function createPage(Request $request){
//     // $request->merge([$pageData]);
//     $request->merge($this->pageData);

//     $pageData = new ProfileController;
//         $response =  $pageData->store($request);
//         $result = $response->getData();
//         $this->statusCode = $result->status_code;
//     dd($request);
//    }

   public function createPage(Request $request){
    // dd($request);
    $request->merge($this->pageData);
    $title = $request->title; 
    // Generate slug from title
    $slug = Str::slug($title, '-');

    // Merge slug into request data
    $request->merge(['slug' => $slug]);
    // dd($request); 
    $pageData = new PageController;
    $response =  $pageData->store($request);
    $result = $response->getData();
    // $status = $result->getStatusCode();
    $this->successMessage = $result->message;
    $this->reset(['content', 'title' ]);
    // dd($result);
}

    public function render()
    {
        // dd('jsdn');
        return view('livewire.admin.dashboard.pages.pages', [
        ]);
    }
}

// TheParchi provide wide range of online service related to Accounting, Invoicing, Inventory Management, Delivery Challans and Retail Software. For details related to pricing kindly visit the pricing section and go through cancellation policy for any cancellation related query. For each service we provide a certain trial period days which helps customers to understand the features before subscribing for paid plan. For data & privacy related queries kindly visit privacy policy section. For support & more information you can write us at support@theparchi.com


