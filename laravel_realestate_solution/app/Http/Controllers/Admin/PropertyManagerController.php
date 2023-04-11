<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\PropertyManager;
use App\Models\Amenities;
use App\Models\Propertytype;
use App\Models\Flags;
use App\Models\Propertycategory;
use App\Models\User;
use Illuminate\Http\Request;

class PropertyManagerController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(PropertyManager::select('*'))
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/propertymanager.index');
    }


    public function create()
    {
        $amenities = Amenities::all();
        $propertytype = Propertytype::all();
        $flags = Flags::all();
        $category = Propertycategory::all();
        $agent = User::where('type', 2)->get();
        $user  = User::where('type', 0)->get();
        return view('admin/pages/propertymanager.create', compact('amenities', 'propertytype', 'flags', 'category', 'user', 'agent'));
    }

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
                'flags' => 'required',
                'image'    => 'required'

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
        $property->user = $request->userid;
        $property->agent = $request->agentid;
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

    public function show($id)
    {
        $property = PropertyManager::find($id);

        $amenities = Amenities::where('id', $property->generalAmenities)->get();
        $propertytype = Propertytype::where('id', $property->ptype)->get();
        $flags = Flags::where('id', $property->propertyflag)->get();
        $category = Propertycategory::where('id', $property->propertycategory)->get();

        $ownerName = User::where('id', $property->agentid)->get()->first();
        $agentName = User::where('id', $property->userId)->get()->first();

        return view('admin/pages/propertymanager.show', compact('property',  'propertytype', 'flags', 'category', 'amenities', 'ownerName', 'agentName'));
    }

    public function edit($id)
    {
        $property = PropertyManager::find($id);

        $amenities = Amenities::all();
        $propertyType = Propertytype::get()->where('status', 1);
        $flags = Flags::all();
        $category = Propertycategory::all();
        $agent = User::where('type', 2)->get();
        $user  = User::where('type', 0)->get();
        return view('admin/pages/propertymanager.edit', compact('property', 'amenities', 'propertyType', 'flags', 'category', 'agent', 'user'));
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
            return redirect()->route('propertymanager.index')
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
            $property->ptype = $request->ptype;
            $property->propertycategory = $request->procategory;
            $property->propertyflag = $request->flags;
            $property->user = $request->userid;
            $property->agent = $request->agentid;
            $property->save();
            return redirect()->route('propertymanager.index')
                ->with('success', 'Property Has Been updated successfully');
        }
    }

    public function destroy(Request $request)
    {
        $com = PropertyManager::where('id', $request->id)->delete();
        return response()->json(['success' => true, 'success' => 'Property deleted successfully']);
    }
}
