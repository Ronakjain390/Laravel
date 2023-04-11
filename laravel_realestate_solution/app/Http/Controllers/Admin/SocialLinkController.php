<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\SocialLink;

use Illuminate\Http\Request;
use Datatables;
use GuzzleHttp\Middleware;

class SocialLinkController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(SocialLink::select('*'))
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/sociallinks.index');
    }


    public function create()
    {
        return view('admin/pages/sociallinks.create');
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
        return redirect()->route('sociallink.index')
            ->with('success', 'Data has been created successfully.');
    }


    public function edit($id)
    {
        $links = SocialLink::find($id);
        return view('admin/pages/sociallinks.edit', compact('links'));
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
            return redirect()->route('sociallink.index')
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
            return redirect()->route('sociallink.index')
                ->with('success', 'Data has Been updated successfully');
        }
    }

    public function destroy(Request $request)
    {
        $com = SocialLink::where('id', $request->id)->delete();
        return response()->json(['delete' => true, 'delete' => 'Data deleted successfully']);
    }
}
