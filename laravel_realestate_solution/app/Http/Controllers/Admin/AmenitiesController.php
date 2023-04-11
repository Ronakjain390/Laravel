<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\Amenities;

use Illuminate\Http\Request;
use Datatables;
use GuzzleHttp\Middleware;

class AmenitiesController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Amenities::select('*'))
                ->addColumn('action', 'admin/pages/amenities.action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/amenities.index');
    }

    public function create()
    {
        return view('admin/pages/amenities.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'amenities' => 'required',
        ]);

        $amenities = new Amenities;
        $amenities->amenities = $request->amenities;
        if ($request->status == 'on') {
            $amenities->status = '1';
        } else {
            $amenities->status = '0';
        }
        $amenities->save();
        return redirect()->route('amenities.index')
            ->with('success', 'Amenities has been created successfully.');
    }

    public function edit($id)
    {
        $amenities = Amenities::find($id);
        return view('admin/pages/amenities.edit', compact('amenities'));
    }

    public function update(Request $request, $id)
    {
        $amenities = Amenities::find($id);
        if ($request->action == 'status_toggle') {
            if ($amenities->status == 0) {
                $amenities->status = 1;
            } else {
                $amenities->status = 0;
            }
            $amenities->save();
            return redirect()->route('amenities.index')
                ->with('success', 'Amenities status has been changed successfully.');
        } else {
            $amenities->amenities = $request->amenities;
            if ($request->status == 'on') {
                $amenities->status = '1';
            } else {
                $amenities->status = '0';
            }
            $amenities->save();
            return redirect()->route('amenities.index')
                ->with('success', 'Amenities Has Been updated successfully');
        }
    }

    public function destroy(Request $request)
    {
        $com = Amenities::where('id', $request->id)->delete();
        return Response()->json($com);
    }
}
