<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Portal;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function getCmsPage($slug){
        $data = Portal::where('slug', $slug)->where('status',1)->first();
        // print_r($data);
        
        return view('user/pages/cmspages.view',compact('data'));
    }
}
