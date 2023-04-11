<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertyManager;
use App\Models\Amenities;
use App\Models\User;
use App\Models\Propertytype;
use App\Models\Flags;
use App\Models\Propertycategory;
use Auth;

class PostPropertyController extends Controller
{
    public function index()
    {
        $agents = User::get()->where('type', 2);
        $amenities = Amenities::all();
        $property = PropertyManager::all();
        $propertytype = Propertytype::all();
        $flags = Flags::all();
        $category = Propertycategory::all(); 
        if(Auth::user()) {
            return view('user/pages/property/postproperty', compact('amenities', 'property', 'agents',  'flags', 'category', 'propertytype'));
        } else {
            return view('auth/userlogin');
        }
    }
    
   
    public function postproperty(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|min:2',
                'email' => ['required', 'email'],
                'contactNumber' => ['required', 'integer'],
                'title' => 'required',
                'reason' => 'required',
                'address' => 'required',
                'builtArea' => ['required', 'integer'],
                'carpetArea' => ['required', 'integer'],
                'bedroom' => ['required', 'integer'],
                'bathroom' => ['required', 'integer'],
                'totalPrice' => ['required', 'integer'],
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'priceType' => 'required'
            ],
            [
                'name.min' => 'Minimum 2 character in required',
                'email.email' => '@ and . is required',
                'contactNumber.integer' => 'Only integer value is allowed',
                'builtArea.integer' => 'Only integer value is allowed',
                'carpetArea.integer' => 'Only integer value is allowed',
                'bedroom.integer' => 'Only integer value is allowed',
                'bathroom.integer' => 'Only integer value is allowed',
                'totalPrice.integer' => 'Only integer value is allowed'
            ]
        );
        $file_name = time() . '.' . request()->file('image')->getClientOriginalExtension();

        request()->image->move(public_path('assets/images/property/'), $file_name);
        $property = new PropertyManager;
        $property->name = $request->name;
        $property->agentid = $request->agentid;
        $property->userId = Auth::user()->id;
        $property->email = $request->email;
        $property->contactNumber = $request->contactNumber;
        $property->title = $request->title;
        $property->Utype = 'owner';
        $property->reason = $request->reason;
        $property->address = $request->address;
        $property->builtArea = $request->builtArea;
        $property->carpetArea = $request->carpetArea;
        $property->bedroom = $request->bedroom;
        $property->bathroom = $request->bathroom;
        $property->priceType = $request->priceType;
        $property->totalPrice = $request->totalPrice;
        $property->description = $request->description;
        $property->generalAmenities = $request->amenities;
        $property->longitude = $request->longitude;
        $property->latitude = $request->latitude;
        $property->city = $request->city;
        $property->state = $request->state;
        $property->country = $request->country;
        $property->postal_code = $request->postal_code;
        $property->ptype = $request->ptype;
        $property->image = $file_name;
        $property->propertycategory = $request->procategory;
        $property->propertyflag = $request->flags;
        $property->status = 1;
        $property->save();
        return redirect()->route('userPropertyList')
            ->with('success', 'Property has been created successfully.');
    }

}
