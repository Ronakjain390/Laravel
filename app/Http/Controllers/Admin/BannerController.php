<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\Banners;

use Illuminate\Http\Request;
use Datatables;
use GuzzleHttp\Middleware;

class BannerController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Banners::select('*'))
                ->rawColumns(['action'])
                ->escapeColumns('aaData')
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/banners.index');
    }

    public function create()
    {
        return view('admin/pages/banners.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'image' => 'required',
            'description' => 'required',
        ]);
        $file_name = time() . '.' . request()->file('image')->getClientOriginalExtension();

        request()->image->move(public_path('assets/images/banner/'), $file_name);

        $banner = new Banners;
        $banner->title = $request->title;
        $banner->image = $file_name;
        $banner->description = $request->description;
        if ($request->status == 'on') {
            $banner->status = '1';
        } else {
            $banner->status = '0';
        }
        $banner->save();
        return redirect()->route('banners.index')
            ->with('success', 'Banner has been created successfully.');
    }

    public function show(Banners $banner)
    {
        return view('admin/pages/banners.show', compact('banner'));
    }
    public function edit(Banners $banner)
    {
        return view('admin/pages/banners.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $banner = Banners::find($id);
        if ($request->action == 'status_toggle') {
            if ($banner->status == 0) {
                $banner->status = 1;
            } else {
                $banner->status = 0;
            }
            $banner->save();
            return redirect()->route('banners.index')
                ->with('success', 'Status has been changed successfully.');
        } else {
            $Image = $request->hidden_Image;

            if ($request->image != '') {
                $Image = time() . '.' . request()->image->getClientOriginalExtension();

                request()->image->move(public_path('assets/images/banner/'), $Image);
            }
            $banner = Banners::find($id);
            $banner->title = $request->title;
            $banner->image = $Image;
            $banner->description = $request->description;
            if ($request->status == 'on') {
                $banner->status = 1;
            } else {
                $banner->status = 2;
            }
            $banner->save();
            return redirect()->route('banners.index')
                ->with('success', 'Banner Has Been updated successfully');
        }
    }

    public function destroy(Request $request)
    {
        $com = Banners::where('id', $request->id)->delete();
        return response()->json(['success' => true, 'success' => 'Article deleted successfully']);
    }
}
