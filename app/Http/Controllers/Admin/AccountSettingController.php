<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Auth;

class AccountSettingController extends Controller
{
    public function index()
    {
        $accountsetting = User::get()->where('id', Auth::user()->id)->where('type', '1')->first();
        return view('admin/pages/accountsetting.index', compact('accountsetting'));
    }
    public function edit($id)
    {
        $accountsetting = User::find($id);
        return view('admin/pages/accountsetting.edit', compact('accountsetting'));
    }
    public function update(Request $request, $id)
    {
        $accountsetting = User::find($id);
        $Image = $request->hidden_Image;

        if ($request->image != '') {
            $Image = time() . '.' . request()->image->getClientOriginalExtension();

            request()->image->move(public_path('assets/images/admin/'), $Image);
        }

        $accountsetting->image = $Image;
        $accountsetting->name = $request->name;
        $accountsetting->email = $request->email;
        $accountsetting->address = $request->address;
        $accountsetting->city = $request->city;
        $accountsetting->state = $request->state;
        $accountsetting->password = Hash::make($request->password);
        $accountsetting->contact = $request->contact;
        $accountsetting->country = $request->country;
        $accountsetting->postal_code = $request->postal_code;
        $accountsetting->type = $request->type;
        $accountsetting->username = $request->username;
        $accountsetting->save();
        return redirect()->route('accountsetting.index')
            ->with('success', 'Account Has Been updated successfully');
    }
}
