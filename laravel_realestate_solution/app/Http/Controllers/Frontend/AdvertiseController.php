<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Advertise;
use App\Models\Amenities;
use Illuminate\Http\Request;

class AdvertiseController extends Controller
{
    public function index()
    {
        $amenities = Amenities::all();
        return view('user/pages/advertise.create' , compact('amenities'));
    }
    // Store 
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'contactNumber' => 'required',
            'title' => 'required',
            'Utype' => 'required',
            'reason' => 'required',
            'location' => 'required',
            'builtArea' => 'required',
            'carpetArea' => 'required',
            'bedroom' => 'required',
            'bathroom' => 'required',
            'totalprice' => 'required',
        ]);
        $file_name = time() . '.' . request()->file('image')->getClientOriginalExtension();

        request()->image->move(public_path('images/'), $file_name);

        $advertise = new Advertise;
        $advertise->name = $request->name;
        $advertise->email = $request->email;
        $advertise->contactNumber = $request->contactNumber;
        $advertise->title = $request->title;
        $advertise->Utype = $request->Utype;
        $advertise->reason = $request->reason;
        $advertise->location = $request->location;
        $advertise->builtArea = $request->builtArea;
        $advertise->carpetArea = $request->carpetArea;
        $advertise->bedroom = $request->bedroom;
        $advertise->bathroom = $request->bathroom;
        $advertise->parking = $request->parking;
        $advertise->priceType = $request->priceType;
        $advertise->totalprice = $request->totalprice;
        $advertise->description = $request->description;
        $advertise->generalAmenities = $request->generalAmenities;
        $advertise->longitude = $request->longitude;
        $advertise->latitude = $request->latitude;
        $advertise->image = $file_name;
        $advertise->save();
        return redirect()->route('advertise.create')
            ->with('success', 'Property has been created successfully.');
    }
}
