<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;


use App\Models\User;

use Illuminate\Http\Request;

class AgentManagerController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(User::select('*')->where('type', 2))
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/agents.index');
    }

    public function create()
    {
        return view('admin/pages/agents.create');
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

        request()->image->move(public_path('assets/images/agents/'), $file_name);

        $agent = new User;
        $agent->image = $file_name;
        $agent->name = $request->name;
        $agent->email = $request->email;
        $agent->address = $request->address;
        $agent->city = $request->city;
        $agent->state = $request->state;
        $agent->password = Hash::make($request->password);
        $agent->contact = $request->contact;
        $agent->country = $request->country;
        $agent->postal_code = $request->postal_code;
        $agent->type = $request->type;
        $agent->username = $request->username;
        if ($request->status == 'on') {
            $agent->status = 1;
        } else {
            $agent->status = 0;
        }
        $agent->save();
        return redirect()->route('agents.index')
            ->with('success', 'Agent has been created successfully.');
    }

    public function show($id)
    {
        $agent = User::find($id);

        return view('admin/pages/agents.show', compact('agent'));
    }
    public function edit($id)
    {
        $agent = User::find($id);

        return view('admin/pages/agents.edit', compact('agent'));
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
            return redirect()->route('agents.index')
                ->with('success', 'User status has been changed successfully.');
        } else {
            $Image = $request->hidden_Image;

            if ($request->image != '') {
                $Image = time() . '.' . request()->image->getClientOriginalExtension();

                request()->image->move(public_path('assets/images/agents/'), $Image);
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
            return redirect()->route('agents.index')
                ->with('success', 'Agent has been updated successfully.');
        }
    }

    public function destroy(Request $request)
    {
        $com = User::where('id', $request->id)->delete();
        return response()->json(['success' => true, 'success' => 'Agent deleted successfully']);
    }
    protected $dates = [
        'created_at',
    ];

    public function getCreatedFormatAttribute()
    {  
        return $this->created_at->format('d-m-Y');
    }
  protected $appends = ['created_format'];
}
