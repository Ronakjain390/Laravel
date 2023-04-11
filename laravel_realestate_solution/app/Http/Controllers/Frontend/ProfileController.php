<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PropertyManager;
use App\Models\Propertytype;
use App\Models\Flags;
use App\Models\Propertycategory;
use Illuminate\Support\Facades\Auth;
use App\Models\Amenities;


class ProfileController extends Controller
{
    public function indexProfile()
    {
        $profile = User::get()->where('id', Auth::user()->id)->where('type', '0')->first();
        $property = PropertyManager::all();
        $propertytype = Propertytype::all();
        $flags = Flags::all();
        $category = Propertycategory::all();
        return view('user/pages/profile/profileUser', compact('profile',  'flags', 'category', 'propertytype'));
    }

    public function profileUpdate(Request $request, $id)
    {
        $Image = $request->hidden_Image;

        if ($request->image != '') {
            $Image = time() . '.' . request()->image->getClientOriginalExtension();

            request()->image->move(public_path('assets/images/users/'), $Image);
        }
        $profile = User::find($id);
        $profile->image = $Image;
        $profile->email = $request->email;
        $profile->name = $request->name;
        $profile->contact = $request->contact;
        $profile->country = $request->country;
        $profile->address = $request->address;
        $profile->state = $request->state;
        $profile->city = $request->city;
        $profile->postal_code = $request->postal_code;

        $profile->save();
        return redirect()->back()
            ->with('success', 'Account Has Been updated successfully');
    }

    public function userListedProperty()
    {
        $properties = PropertyManager::get()->where('userId', Auth::user()->id);
        return view('user/pages/profile/userListingProperty', compact('properties'));
    }
    public function create()
    {
        $agents = User::where('type', 2)->get();
        $amenities = Amenities::all();
        $propertytype = Propertytype::all();
        $flags = Flags::all();
        $category = Propertycategory::all();
        return view('user/pages/profile/userProperty', compact('agents', 'amenities', 'propertytype', 'flags', 'category'));
    }

    public function userShowProperty($id)
    {
        $property = PropertyManager::find($id);
        $amenities = Amenities::all();
        $propertytype = Propertytype::all();
        $flags = Flags::all();
        $category = Propertycategory::all();

        $agentName = User::where('id', $property->agentid)->get()->first();

        return view('user/pages/profile/propertyShow', compact('property', 'amenities', 'flags', 'propertytype', 'category', 'agentName'));
    }

    public function userDeleteProperty($id)
    {
        $property = PropertyManager::find($id)->delete();
        return redirect()->route('userPropertyList')->with('success', 'Property deleted successufully');
    }

    public function userEditProperty($id)
    {
        $agents = User::get()->where('type', 2);
        $property = PropertyManager::find($id);
        $amenities = Amenities::where('id', $property->generalAmenities)->first()->get();
        $propertytype = Propertytype::all();
        $flags = Flags::all();
        $category = Propertycategory::all();
        return view('user/pages/profile/propertyEdit', compact('property', 'amenities', 'agents', 'propertytype', 'flags', 'category'));
    }

    public function userUpdateProperty(Request $request, $id)
    {
        $Image = $request->hidden_Image;

        if ($request->image != '') {
            $Image = time() . '.' . request()->image->getClientOriginalExtension();

            request()->image->move(public_path('assets/images/property/'), $Image);
        }
        $updateProperty = PropertyManager::find($id);
        $updateProperty->image = $Image;
        $updateProperty->name = $request->name;
        $updateProperty->email = $request->email;
        $updateProperty->contactNumber = $request->contactNumber;
        $updateProperty->title = $request->title;
        $updateProperty->Utype = 'owner';
        $updateProperty->reason = $request->reason;
        $updateProperty->address = $request->address;
        $updateProperty->builtArea = $request->builtArea;
        $updateProperty->carpetArea = $request->carpetArea;
        $updateProperty->bedroom = $request->bedroom;
        $updateProperty->bathroom = $request->bathroom;
        $updateProperty->priceType = $request->priceType;
        $updateProperty->totalPrice = $request->totalPrice;
        $updateProperty->description = $request->description;
        $updateProperty->generalAmenities = $request->generalAmenities;
        $updateProperty->longitude = $request->longitude;
        $updateProperty->latitude = $request->latitude;
        $updateProperty->city = $request->city;
        $updateProperty->state = $request->state;
        $updateProperty->country = $request->country;
        $updateProperty->postal_code = $request->postal_code;
        $updateProperty->agentid = $request->agent;
        if ($request->status == 'on') {
            $updateProperty->status == 1;
        } else {
            $updateProperty->status == 0;
        }
        $updateProperty->save();
        return redirect()->route('userPropertyList')->with('success', "Property Updated successfully.");
    }

    public function userStatusUpdateProperty(Request $request, $id)
    {
        $property = PropertyManager::find($id);
        if ($request->action == 'status_toggle') {
            if ($property->status == 0) {
                $property->status = 1;
            } else {
                $property->status = 0;
            }
            $property->save();
            return redirect()->route('userPropertyList')
                ->with('success', 'Status has been changed successfully.');
        }
    }
}
