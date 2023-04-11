<?php

namespace App\Http\Controllers\agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AgentProfileController extends Controller
{
    public function index()
    {
        $profile = User::get()->where('id', Auth::user()->id)->where('type', '2')->first();
        return view('agent/pages/profile/index', compact('profile'));
    }

    public function edit($id)
    {
        $profile = User::find($id);
        return view('agent/pages/profile.edit', compact('profile'));
    }

    public function update(Request $request, $id)
    {
        $Image = $request->hidden_Image;

        if ($request->image != '') {
            $Image = time() . '.' . request()->image->getClientOriginalExtension();

            request()->image->move(public_path('assets/images/agent/'), $Image);
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
        return redirect()->route('agentProfile')
            ->with('success', 'Account Has Been updated successfully');
    }
}
