<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    public function index()
    {
        $adminProfile = User::all();
        return view('SuperAdmin/Pages/Profile/Index', compact('adminProfile'));
    }

    public function updateprofile(Request $request, $id)
    {
        // echo $request->image; die;
        $user = User::find($id);
        $user->name = $request->name;
        $user->contact = $request->contact_number;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->country = $request->country;
        $user->postal_code = $request->postal_code;

        $image = $request->hidden_Image;

        if ($request->image != '') {
            $image = time() . '.' . request()->image->getClientOriginalExtension();

            request()->image->move(public_path('Assets/images'), $image);
        }
        $user->image = $image;
        $user->save();
        // echo $user->image; die;
        $oldpassword = auth()->user()->password;
        if (!Hash::check($request->old_password, auth()->user()->password)) {
        return back()->with("error", "Old Password Doesn't match!");
        }
        if ($request->new_password != '') {
        $oldpassword = $request->new_password;
        }
        $user->password = Hash::make($oldpassword); 
        $user->image = $image;
        // print_r($user); die;
       
        return redirect()->back()
            ->with('success', 'Account Has Been updated successfully');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('superAdminLogin');
    }
}
