<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $generalsetting = GeneralSetting::first();

        // echo $generalsetting;
        // die;
        return view('SuperAdmin/Pages/General_Settings/Index', compact('generalsetting'));
    }

    public function edit($id = null)
    {
        if ($id) {
            $generalsetting = GeneralSetting::where('id',$id)->first();

            return view('SuperAdmin/Pages/General_Settings/Edit', compact('generalsetting'));
        }
        return view('SuperAdmin/Pages/General_Settings/Edit');
    }

    public function update(Request $request, $id = null)
    {
        // echo $request->favicon;
        // die;
        if ($id) {
            $data = GeneralSetting::find($id);
            $logo = $request->hidden_logo;
            $logo_mini = $request->hidden_logo_mini;
            $favicon = $request->hidden_favicon;
            if ($request->logo != '') {
                $logo = time() . '.' . request()->logo->getClientOriginalExtension();

                request()->logo->move(public_path('Assets/images/'), $logo);
            }
            if ($request->favicon != '') {
                $favicon = time() . '.' . request()->favicon->getClientOriginalExtension();

                request()->favicon->move(public_path('Assets/images/'), $favicon);
            }
            if ($request->logo_mini != '') {
                $logo_mini = time() . '.' . request()->logo_mini->getClientOriginalExtension();

                request()->logo_mini->move(public_path('Assets/images/'), $logo_mini);
            }
            $data->logo = $logo;
            $data->favicon = $favicon;
            $data->logo_mini = $logo_mini;
            $data->copyright_text = $request->copyright_text;
            $data->title = $request->title;
            $data->address = $request->address;
            $data->country = $request->country;
            $data->state = $request->state;
            $data->city = $request->city;
            $data->zipCode = $request->zipCode;
            $data->longitude = $request->longitude;
            $data->latitude = $request->latitude;
            $data->email = $request->email;
            $data->date_format = $request->date_format;
            $data->contact_number = $request->contact_number;
            $data->save();
            return redirect()->route('GeneralSetting')
                ->with('success', 'General Setting Has Been updated successfully');
        }
        $logo = time() . '.' . request()->logo->getClientOriginalExtension();

        request()->logo->move(public_path('Assets/images/'), $logo);


        $favicon = time() . '.' . request()->favicon->getClientOriginalExtension();

        request()->favicon->move(public_path('Assets/images/'), $favicon);


        $logo_mini = time() . '.' . request()->logo_mini->getClientOriginalExtension();

        request()->logo_mini->move(public_path('Assets/images/'), $logo_mini);

        $data = new GeneralSetting;

        $data->logo = $logo;
        $data->favicon = $favicon;
        $data->logo_mini = $logo_mini;
        $data->copyright_text = $request->copyright_text;
        $data->title = $request->title;
        $data->address = $request->address;
        $data->country = $request->country;
        $data->state = $request->state;
        $data->city = $request->city;
        $data->zipCode = $request->zipCode;
        $data->longitude = $request->longitude;
        $data->latitude = $request->latitude;
        $data->email = $request->email;
        $data->date_format = $request->date_format;
        $data->contact_number = $request->contact_number;
        $data->save();
        return redirect()->route('GeneralSetting')
            ->with('success', 'General Setting Has Been updated successfully');
    }
}
