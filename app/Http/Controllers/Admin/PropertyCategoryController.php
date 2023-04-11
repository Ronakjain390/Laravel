<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Propertycategory;

class PropertyCategoryController extends Controller
{
    public function index()
    {
        $data = Propertycategory::all();
        return view('admin/pages/propertycategory.index', compact('data'));
    }

    public function create()
    {
        return view('admin/pages/propertycategory.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'category_name' => 'required',
        ]);
        $pctype = new Propertycategory;
        $pctype->category_name = $request->category_name;
        $pctype->save();
        return redirect()->route('PropertyCategory')
            ->with('success', 'Property Category has been created successfully.');
    }

    public function Delete($id)
    {
        $com = Propertycategory::where('id', $id)->delete();
        return redirect()->route('PropertyCategory')->with('delete', "Record Deleted Succesfully.");
    }
}
