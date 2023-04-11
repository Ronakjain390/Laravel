<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use Datatables;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function index()
    {
       $cms = Cms::all();
        return view('SuperAdmin/Pages/Cms/Index', compact('cms'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('SuperAdmin/Pages/Cms/Create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    //    dd($request);
        $this->validate($request, [
            'name' => 'required',
        ]);
        $image = time() . '.' . request()->file('image')->getClientOriginalExtension();

        request()->image->move(public_path('Assets/images/'), $image);
        $cms = new Cms();
        $cms->name = $request->name;
        $cms->slug = $this->createSlug($cms->name);
        $cms->description = $request->description;
        $cms->image = $image;
        if ($request->status == 'on') {
            $cms->status = 1;
        } else {
            $cms->status = 0;
        }
        $cms->save();
        return redirect()->route('Cms')
            ->with('success', 'Portal has been created successfully.');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\company  $company
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cms = Cms::where('id', $id)->get();
        return view('SuperAdmin/Pages/Cms/Show', compact('cms'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cmsEdit = Cms::find($id);
        return view('SuperAdmin/Pages/Cms/Edit', compact('cmsEdit'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cms = Cms::find($id);
        if ($request->status == 'on') {
            $cms->status = '1';
        } else {
            $cms->status = '0';
        }
        $image = $request->hidden_Image;

        if ($request->image != '') {
            $image = time() . '.' . request()->image->getClientOriginalExtension();

            request()->image->move(public_path('Assets/images/'), $image);
        }
            $cms->name = $request->name;
            $cms->description = $request->description;
            $cms->image = $image;
            $cms->save();
            return redirect()->route('Cms')
                ->with('success', 'CMS Has Been updated successfully');
        }
    
    public function destroy(Request $request)
    {
        $com = Cms::where('id', $request->id)->delete();
        return redirect()->back()->with('error', 'CMS deleted successful');
    }
    public function createSlug($title, $id = 0)
    {
        $slug = str_slug($title);
        $allSlugs = $this->getRelatedSlugs($slug, $id);
        if (!$allSlugs->contains('slug', $slug)) {
            return $slug;
        }

        $i = 1;
        $is_contain = true;
        do {
            $newSlug = $slug . '-' . $i;
            if (!$allSlugs->contains('slug', $newSlug)) {
                $is_contain = false;
                return $newSlug;
            }
            $i++;
        } while ($is_contain);
    }
    protected function getRelatedSlugs($slug, $id = 0)
    {
        return Cms::select('slug')->where('slug', 'like', $slug . '%')
            ->where('id', '<>', $id)
            ->get();
    }
}
