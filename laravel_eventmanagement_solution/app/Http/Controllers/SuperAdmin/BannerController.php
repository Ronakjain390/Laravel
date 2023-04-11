<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class BannerController extends Controller
{
    public function index()
    {
        $data = Banner::all();
        return view('SuperAdmin/Pages/Banner/Index', compact('data'));
    }

    public function create()
    {
        return view('SuperAdmin/Pages/Banner/Create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'image' => 'required',
            'description' => 'required',
        ]);
        $file_name = time() . '.' . request()->file('image')->getClientOriginalExtension();

        request()->image->move(public_path('Assets/images/'), $file_name);

        $banner = new Banner;
        $banner->title = $request->title;
        $banner->image = $file_name;
        $banner->description = $request->description;
        if ($request->status == 'on') {
            $banner->status = '1';
        } else {
            $banner->status = '0';
        }
        $banner->save();
        return redirect()->route('Banners')
            ->with('success', 'Banner has been created successfully.');
    }

    public function edit($id)
    {
        $data = Banner::find($id);
        return view('SuperAdmin/Pages/Banner/Edit', compact('data'));
    }

    public function show($id)
    {
        $data = Banner::where('id', $id)->get();
        return view('SuperAdmin/Pages/Banner/Show', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $banner = Banner::find($id);
        if ($request->status == 'on') {
            $banner->status = '1';
        } else {
            $banner->status = '0';
        }
        $image = $request->hidden_Image;

        if ($request->image != '') {
            $image = time() . '.' . request()->image->getClientOriginalExtension();

            request()->image->move(public_path('Assets/images/'), $image);
        }
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->image = $image;
        $banner->save();

        return redirect()->route('Banners')
            ->with('success', 'Banner Has Been updated successfully');
    }

    public function destroy($id)
    {
  
        $com = Banner::where('id', $id)->delete();
        return redirect()->back()->with('error', 'Banner deleted successfully');
    }
}
