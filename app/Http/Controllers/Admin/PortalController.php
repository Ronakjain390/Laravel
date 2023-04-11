<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\Portal;

use Illuminate\Http\Request;
use Datatables;

class PortalController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Portal::select('*'))
                ->rawColumns(['action'])
                ->escapeColumns('aaData')
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/portals.index');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin/pages/portals.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function portalsStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'contents' => 'required', 
        ]); echo 1;die;
        $image = time() . '.' . request()->file('image')->getClientOriginalExtension();

        request()->image->move(public_path('assets/images/portal/'), $image);
        $portal = new Portal();
        $portal->name = $request->name;
        $portal->slug = $this->createSlug($portal->name);
        $portal->content_editor = $request->contents;
        $portal->type == $request->type;
        $portal->image = $image; 
        if ($request->status == 'on') {
            $portal->status = 1;
        } else {
            $portal->status = 0;
        }
        $portal->save();
        return redirect()->route('portals.index')
            ->with('success', 'Portal has been created successfully.');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Portal $portal)
    {
        return view('admin/pages/portals.show', compact('portal'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Portal $portal)
    {
        return view('admin/pages/portals.edit', compact('portal'));
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
        $portal = Portal::find($id);
        if ($request->action == 'status_toggle') {
            if ($portal->status == 0) {
                $portal->status = 1;
            } else {
                $portal->status = 0;
            }
            $portal->save();
            return redirect()->route('portals.index')
                ->with('success', 'User status has been changed successfully.');
        } else {
            $portal->name = $request->name;
            $portal->content_editor = $request->content_editor;
            if ($request->status == 'on') {
                $portal->status = 1;
            } else {
                $portal->status = 0;
            }
            $portal->save();
            return redirect()->route('portals.index')
                ->with('success', 'Portal Has Been updated successfully');
        }
    }
    public function destroy(Request $request)
    {
        $com = Portal::where('id', $request->id)->delete();
        return Response()->json($com);
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
        return Portal::select('slug')->where('slug', 'like', $slug . '%')
            ->where('id', '<>', $id)
            ->get();
    }
}
