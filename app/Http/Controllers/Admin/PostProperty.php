<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\PropertyManager;

use Illuminate\Http\Request;

class PostProperty extends Controller
{
    public function CreateProperty(Request $request)
    {
        $this->validate( 
            $request, 
            [
                'name' => 'required|min:2',
                'email' => ['required', 'email'],
                'contactNumber' => ['required', 'numeric'],
                'title' => 'required',
                'Utype' => 'required',
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
                'priceType' => 'required',
                'ptype' => 'required',
                'procategory' => 'required',
                'flags' => 'required'               

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
        $property->email = $request->email;
        $property->contactNumber = $request->contactNumber;
        $property->title = $request->title;
        $property->Utype = $request->Utype;
        $property->reason = $request->reason;
        $property->address = $request->address;
        $property->builtArea = $request->builtArea;
        $property->carpetArea = $request->carpetArea;
        $property->bedroom = $request->bedroom;
        $property->bathroom = $request->bathroom;
        $property->priceType = $request->priceType;
        $property->totalPrice = $request->totalPrice;
        $property->description = $request->description;
        $property->generalAmenities = $request->generalAmenities;
        $property->longitude = $request->longitude;
        $property->latitude = $request->latitude;
        $property->city = $request->city;
        $property->state = $request->state;
        $property->country = $request->country;
        $property->postal_code = $request->postal_code;
        $property->ptype = $request->ptype;
        $property->userId = $request->userid;
        $property->agentid = $request->agentid;
        $property->image = $file_name;
        if ($request->status == 'on') {
            $property->status = 1;
        } else {
            $property->status = 0;
        }
        $property->save();

        return redirect()->route('propertymanager.index')
            ->with('success', 'Property has been created successfully.');
    }
}
