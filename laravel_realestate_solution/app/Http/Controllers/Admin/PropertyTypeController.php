<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Propertytype;
use App\Models\Propertycategory;

class PropertyTypeController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Propertytype::select('*'))
                ->rawColumns(['action'])
                ->escapeColumns('aaData')
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/propertytypes.index');
    }

    public function create()
    {
        $category = Propertycategory::all();
        return view('admin/pages/propertytypes.create', compact('category'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'image' => 'required',
        ]);


        $file_name = time() . '.' . request()->file('image')->getClientOriginalExtension();

        request()->image->move(public_path('assets/images/propertytype/'), $file_name);


        $ptype = new Propertytype;
        $ptype->name = $request->name;
        $ptype->description = $request->description;
        $ptype->propertycategoryid = $request->category;
        $ptype->image = $file_name;
        if ($request->status == 'on') {
            $ptype->status = 1;
        } else {
            $ptype->status = 0;
        }
        $ptype->save();
        return redirect()->route('propertytypes.index')
            ->with('success', 'Property type has been created successfully.');
    }

    public function show($id)
    {
        $ptype = Propertytype::find($id);
        $category = Propertycategory::all();
        return view('admin/pages/propertytypes.show', compact('ptype', 'category'));
    }

    public function edit($id)
    {
        $ptype = Propertytype::find($id);
        $category = Propertycategory::all();
        return view('admin/pages/propertytypes.edit', compact('ptype', 'category'));
    }

    public function update(Request $request, $id)
    {

        $ptype = Propertytype::find($id);
        if ($request->action == 'status_toggle') {
            if ($ptype->status == 0) {
                $ptype->status = 1;
            } else {
                $ptype->status = 0;
            }
            $ptype->save();
            return redirect()->route('propertytypes.index')
                ->with('success', 'Status has been changed successfully.');
        } else {
            $Image = $request->hidden_Image;

            if ($request->image != '') {
                $Image = time() . '.' . request()->image->getClientOriginalExtension();

                request()->image->move(public_path('assets/images/propertytype/'), $Image);
            }
            $ptype->name = $request->name;
            $ptype->description = $request->description;
            $ptype->category_name = $request->category;
            $ptype->image = $Image;
            if ($request->status == 'on') {
                $ptype->status = 1;
            } else {
                $ptype->status = 0;
            }
            $ptype->save();
            return redirect()->route('propertytypes.index')
                ->with('success', 'Property type Has Been updated successfully');
        }
    }

    public function destroy(Request $request)
    {
        $com = Propertytype::where('id', $request->id)->delete();
        return response()->json(['success' => true, 'success' => 'Property type deleted successfully']);
    }
}
