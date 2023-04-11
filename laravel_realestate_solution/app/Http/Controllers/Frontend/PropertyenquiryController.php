<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Propertyenquiry;
use App\Models\PropertyManager;
use Illuminate\Support\Facades\Validator;


class PropertyenquiryController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required',
                'contact' => 'required',
                'description' => 'required',
            ],
            [
                'name.required' => 'Minimum 2 character are required',
                'email.email' => '@ and . is required',
                'contact.integer' => 'Only integer value is allowed',
                'description.required' => 'Description Field cannot be empty',
            ]
        );
        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        } else {
            $propertyenquiry = new Propertyenquiry();
            $propertyenquiry->name = $request->name;
            $propertyenquiry->email = $request->email;
            $propertyenquiry->contact = $request->contact;
            $propertyenquiry->description = $request->description;
            $propertyenquiry->userId = $request->userId;
            $propertyenquiry->propertyId = $request->propertyId;
            $propertyenquiry->save();
            return redirect()->back()->with('Success', 'Enquiry is added Successfully');
        }
    }
}
