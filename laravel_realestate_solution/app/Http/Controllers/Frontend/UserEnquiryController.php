<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\PropertyManager;
use App\Models\Propertyenquiry;
use Illuminate\Support\Facades\Auth;
class UserEnquiryController extends Controller
{
    public function userEnquiry()
    {
        
        $pro = PropertyManager::get()->where('userId', Auth::user()->id)->pluck('id');
        $property_obj = Propertyenquiry::get()->whereIn('propertyId', $pro);

        $propertyname = PropertyManager::whereIn('id', $pro)->get()->first();
     
        return view('user/pages/profile/userEnquiry',compact('propertyname'))->with('enquiry', $property_obj);
    }
    // public function enquiryShow($id)
    // { 
    //     $pro = Propertyenquiry::find($id);
    //     return view('agent/pages/agentenquiry/show', compact('pro'));
    // }
}
