<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class UserManagerController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(User::select('*')->where('type', 0))
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/users.index');
    }

    public function create()
    {
        return view('admin/pages/users.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => 'required',
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'postal_code' => 'required',
            'password' => 'required',
            'contact' => 'required',
        ]);
        $file_name = time() . '.' . request()->file('image')->getClientOriginalExtension();

        request()->image->move(public_path('assets/images/users/'), $file_name);

        $user = new User;
        $user->image = $file_name;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->password = Hash::make($request->password);
        $user->contact = $request->contact;
        $user->country = $request->country;
        $user->postal_code = $request->postal_code;
        $user->type = $request->type;
        $user->username = $request->username;
        if ($request->status == 'on') {
            $user->status = 1;
        } else {
            $user->status = 0;
        }
        $user->save();
        return redirect()->route('users.index')
            ->with('success', 'User has been created successfully.');
    }

    public function show($id)
    {
        $user = User::find($id);
        return view('admin/pages/users.show', compact('user'));
    }
    public function edit($id)
    {
        $user = User::find($id);
        return view('admin/pages/users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($request->action == 'status_toggle') {
            if ($user->status == 0) {
                $user->status = 1;
            } else {
                $user->status = 0;
            }
            $user->save();
            return redirect()->route('users.index')
                ->with('success', 'User status has been changed successfully.');
        } else {
            $Image = $request->hidden_Image;

            if ($request->image != '') {
                $Image = time() . '.' . request()->image->getClientOriginalExtension();

                request()->image->move(public_path('assets/images/users/'), $Image);
            }

            $user->image = $Image;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->address = $request->address;
            $user->city = $request->city;
            $user->state = $request->state;
            $user->password = Hash::make($request->password);
            $user->contact = $request->contact;
            $user->country = $request->country;
            $user->postal_code = $request->postal_code;
            $user->type = $request->type;
            $user->username = $request->username;
            if ($request->status == 'on') {
                $user->status = 1;
            } else {
                $user->status = 0;
            }
            $user->save();
            return redirect()->route('users.index')
                ->with('success', 'User has been updated successfully.');
        }
    }

    public function destroy(Request $request)
    {
        $com = User::where('id', $request->id)->delete();
        return response()->json(['delete' => true, 'delete' => 'User deleted successfully']);
    }
}
