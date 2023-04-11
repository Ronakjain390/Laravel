<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PopularDestination;

class DestinationController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(PopularDestination::select('*'))
                ->rawColumns(['action'])
                ->escapeColumns('aaData')
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/destinations.index');
    }

    public function create()
    {
        return view('admin/pages/destinations.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'image' => 'required',
            'description' => 'required',
            'address' => 'required',
        ]);

        $destination = new PopularDestination;
        $destination->title = $request->title;
        if ($request->hasfile('image')) {
            foreach ($request->file('image') as $file) {
                $name = $file->getClientOriginalName();
                $file->move(public_path('assets/images/destinations/'), $name);
                $imgData[] = $name;
            }

            $destination->image = json_encode($imgData);
        }
        $destination->description = $request->description;
        $destination->address = $request->address;
        $destination->save();
        return redirect()->route('destinations.index')
            ->with('success', 'Destination has been created successfully.');
    }

    public function show(PopularDestination $destination)
    {

        return view('admin/pages/destinations.show', compact('destination'));
    }

    public function edit(PopularDestination $destination)
    {

        return view('admin/pages/destinations.edit', compact('destination'));
    }

    public function update(Request $request, $id)
    {
        $file_name = $request->hidden_Image;

        $destination = PopularDestination::find($id);
        if ($request->image != '' && isset($request->image)) {
            if ($request->hasfile('image')) {
                foreach ($request->file('image') as $file) {
                    $name = $file->getClientOriginalName();
                    $file->move(public_path('assets/images/destinations/'), $name);
                    $imgData[] = $name;
                }

                $destination->image = json_encode($imgData);
            }
        }

        $destination->title = $request->title;
        $destination->address = $request->address;
        $destination->description = $request->description;
        $destination->save();
        return redirect()->route('destinations.index')
            ->with('success', 'Banner Has Been updated successfully');
    }

    public function destroy(Request $request)
    {
        $com = PopularDestination::where('id', $request->id)->delete();
        return response()->json(['success' => true, 'success' => 'Destination deleted successfully']);
    }
}
