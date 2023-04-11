<?php

namespace App\Http\Controllers\agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertyManager;
use App\Models\Amenities;
use App\Models\User;
use App\Models\Propertytype;
use App\Models\Flags;
use App\Models\Propertycategory;
use Illuminate\Support\Facades\Auth;

class AgentPropertyController extends Controller
{
    public function index()
    {
        $property = PropertyManager::get()->where('agentid', Auth::user()->id);
        return view('agent/pages/property/index', compact('property'));
    }


    public function create()
    {
        $agents = User::get()->where('type', 0)->where('status', 1);
        $amenities = Amenities::all();
        $propertytype = Propertytype::all();
        $flags = Flags::all();
        $category = Propertycategory::all();
        return view('agent/pages/property.create', compact('amenities', 'agents', 'flags', 'category', 'propertytype'));
    }

    public function store(Request $request)
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
                'priceType' => 'required',
                'image' => 'required'
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
        if ($request->image) {
            $file_name = time() . '.' . request()->file('image')->getClientOriginalExtension();

            request()->image->move(public_path('assets/images/property/'), $file_name);
        } else {
            $file_name = null;
        }
        $property = new PropertyManager;
        $property->name = $request->name;
        $property->userId = $request->userId;
        $property->agentid = Auth::user()->id;
        $property->email = $request->email;
        $property->contactNumber = $request->contactNumber;
        $property->title = $request->title;
        $property->Utype = 'agent';
        $property->reason = $request->reason;
        $property->address = $request->address;
        $property->builtArea = $request->builtArea;
        $property->carpetArea = $request->carpetArea;
        $property->priceType = $request->priceType;
        $property->totalPrice = $request->totalPrice;
        $property->description = $request->description;
        $property->generalAmenities = $request->generalAmenities;
        if ($request->longitude) {
            $property->longitude = $request->longitude;
        }
        if ($request->latitude) {
            $property->latitude = $request->latitude;
        }
        $property->city = $request->city;
        $property->state = $request->state;
        $property->country = $request->country;
        $property->postal_code = $request->postal_code;
        $property->image = $file_name;
        $property->status = 1;

        $property->bedroom = $request->bedroom;
        $property->bathroom = $request->bathroom;
        $property->save();
        return redirect()->route('property.index')
            ->with('success', 'Property has been created successfully.');
    }

    public function show($id)
    {
        $property = PropertyManager::find($id);
        $propertytype = Propertytype::where('id', $property->ptype)->get()->first();
        $flags = Flags::where('id', $property->propertyflag)->get()->first();
        $category = Propertycategory::where('id', $property->propertycategory)->get()->first();
        $amenities = Amenities::where('id', $property->generalAmenities)->get()->first();

        $ownerName = User::where('id', $property->agentid)->get()->first();

        return view('agent/pages/property.show', compact('property',  'propertytype', 'flags', 'category', 'amenities', 'ownerName'));
    }

    public function edit($id)
    {
        $amenities = Amenities::all();
        $owner = User::get()->where('type', 0)->where('status', 1);
        $property = PropertyManager::find($id);
        $propertytype = Propertytype::all();
        $flags = Flags::all();
        $category = Propertycategory::all();
        return view('agent/pages/property.edit', compact('property', 'amenities', 'owner', 'propertytype', 'flags', 'category'));
    }

    public function update(Request $request, $id)
    {
        $user = PropertyManager::find($id);
        if ($request->action == 'status_toggle') {
            if ($user->status == 0) {
                $user->status = 1;
            } else {
                $user->status = 0;
            }
            $user->save();
            return redirect()->route('property.index')
                ->with('success', 'Property status has been changed successfully.');
        } else {
            $Image = $request->hidden_Image;

            if ($request->image != '') {
                $Image = time() . '.' . request()->image->getClientOriginalExtension();

                request()->image->move(public_path('assets/images/property/'), $Image);
            }

            $property = PropertyManager::find($id);

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
            $property->image = $Image;
            $property->postal_code = $request->postal_code;
            $property->userId = $request->userId;
            $property->ptype = $request->ptype;
            $property->propertycategory = $request->procategory;
            $property->propertyflag = $request->flags;
            if ($request->status == 'on') {
                $property->status = 1;
            } else {
                $property->status = 2;
            }
            $property->save();
            return redirect()->route('property.index')
                ->with('success', 'Property Has Been updated successfully');
        }
    }

    public function propertyDestroy($id)
    {
        $com = PropertyManager::find($id)->delete();
        return redirect()->route('property.index')->with('Success', 'Property deleted successfully');
    }

    public function propertyStatus(Request $request, $id)
    {
        $property = PropertyManager::find($id);
        if ($request->action == 'status_toggle') {
            if ($property->status == 0) {
                $property->status = 1;
            } else {
                $property->status = 0;
            }
            $property->save();
            return redirect()->route('property.index')
                ->with('success', 'Status has been changed successfully.');
        }
    }
}
