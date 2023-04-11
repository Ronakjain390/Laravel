<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SocialLink;

class SocialLinkController extends Controller
{
    public function index()
    {
        $data = SocialLink::all();
        return view('SuperAdmin/Pages/Social_Links/Index', compact('data'));
    }

    public function create()
    {
        return view('SuperAdmin/Pages/Social_Links/Create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'url' => 'required',
            'name' => 'required',
        ]);

        $links = new SocialLink;
        $links->name = $request->name;
        $links->url = $request->url;
        if ($request->status == 'on') {
            $links->status = 1;
        } else {
            $links->status = 0;
        }
        $links->save();
        return redirect()->route('SocialLinks')
            ->with('success', 'Data has been created successfully.');
    }

    public function edit($id)
    {
        $data = SocialLink::find($id);
        return view('SuperAdmin/Pages/Social_Links/Edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $user = SocialLink::find($id);
        if ($request->action == 'status_toggle') {
            if ($user->status == 0) {
                $user->status = 1;
            } else {
                $user->status = 0;
            }
            $user->save();
            return redirect()->route('socialLinks')
                ->with('success', 'Status has been changed successfully.');
        } else {
            $links = SocialLink::find($id);
            $links->name = $request->name;
            $links->url = $request->url;
            if ($request->status == 'on') {
                $links->status = 1;
            } else {
                $links->status = 0;
            }
            $links->save();
            return redirect()->route('SocialLinks')
                ->with('success', 'Data has Been updated successfully');
        }
    }

    public function delete($id)
    {
        $com = SocialLink::where('id', $id)->delete();
        return redirect()->back()->with('error', 'SocialLink Has been Deleted Successfully');
    }
}
