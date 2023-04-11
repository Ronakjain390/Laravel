<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use Illuminate\Http\Request;

class CmsFrontController extends Controller
{
    public function getCmsPage($slug){
        $data = Cms::where('slug', $slug)->where('status',1)->first();
        // print_r($data);
        
        return view('User/Pages/Cms/Index',compact('data'));
    }
}
