<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSettingModel;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $generalsetting = GeneralSettingModel::all();
        return view('admin/pages/generalsetting.index', compact('generalsetting'));
    }
    public function edit($id)
    {
        $generalsetting = GeneralSettingModel::find($id);
        return view('admin/pages/generalsetting.edit', compact('generalsetting'));
    }
    public function update(Request $request, $id)
    {   
        $generalsetting = GeneralSettingModel::find($id);
        $logo = $request->hidden_logo;
        $logo_mini = $request->hidden_logo_mini;
        $favicon = $request->hidden_favicon;
        if ($request->logo != '') {
            $logo = time() . '.' . request()->logo->getClientOriginalExtension();

            request()->logo->move(public_path('assets/images/generalsetting/'), $logo);
        }
        if ($request->favicon != '') {
            $favicon = time() . '.' . request()->favicon->getClientOriginalExtension();

            request()->favicon->move(public_path('assets/images/generalsetting/'), $favicon);
        }
        if ($request->logo_mini != '') {
            $logo_mini = time() . '.' . request()->logo_mini->getClientOriginalExtension();

            request()->logo_mini->move(public_path('assets/images/generalsetting/'), $logo_mini);
        }
        $generalsetting->logo = $logo;
        $generalsetting->favicon = $favicon;
        $generalsetting->logo_mini = $logo_mini;
        $generalsetting->copyrightText = $request->copyrightText;
        $generalsetting->title = $request->title;
        $generalsetting->address = $request->address;
        $generalsetting->country = $request->country;
        $generalsetting->state = $request->state;
        $generalsetting->city = $request->city;
        $generalsetting->zipCode = $request->zipCode;
        $generalsetting->longitude = $request->longitude;
        $generalsetting->latitude = $request->latitude;
        $generalsetting->email = $request->email;
        $generalsetting->dateFormat = $request->dateFormat;
        $generalsetting->contactNumber = $request->contactNumber;
        $generalsetting->save();
        return redirect()->route('generalsetting.index')
            ->with('success', 'General Setting Has Been updated successfully');
    }
}
