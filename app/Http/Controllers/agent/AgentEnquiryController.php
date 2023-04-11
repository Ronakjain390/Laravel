<?php

namespace App\Http\Controllers\agent;

use App\Http\Controllers\Controller;
use App\Models\Propertyenquiry;
use App\Models\PropertyManager;
use Illuminate\Support\Facades\Auth;

class AgentEnquiryController extends Controller
{
    public function index()
    {
        $pro = PropertyManager::get()->where('agentid', Auth::user()->id)->pluck('id');
        $property_obj = Propertyenquiry::get()->whereIn('propertyId', $pro);

        return view('agent/pages/agentenquiry/index')->with('enquiry', $property_obj);
    }
    public function enquiryShow($id)
    {
        $pro = Propertyenquiry::find($id);
        $property = PropertyManager::where('id', $pro->propertyId)->get()->first();

        return view('agent/pages/agentenquiry/show', compact('pro','property'));
    }
}
