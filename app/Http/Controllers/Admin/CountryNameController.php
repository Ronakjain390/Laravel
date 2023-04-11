<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CountryName;

class CountryNameController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(CountryName::select('*'))
                ->rawColumns(['action'])
                ->escapeColumns('aaData')
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/countries/index');
    }

    public function create()
    {
        return view('admin/pages/countries.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'countryname' => 'required',
        ]);

        $countryName = new CountryName;
        $countryName->countryname = $request->countryname;
        if ($request->status == 'on') {
            $countryName->status = '1';
        } else {
            $countryName->status = '0';
        }
        $countryName->save();
        return redirect()->route('countries.index')
            ->with('success', 'Country has been created successfully.');
    }

    public function show($id)
    {
        $countryName = CountryName::find($id);
        return view('admin/pages/countries.show', compact('countryName'));
    }


    public function edit($id)
    {
        $countryName = CountryName::find($id);
        return view('admin/pages/countries.edit', compact('countryName'));
    }

    public function update(Request $request, $id)
    {
        $countryName = CountryName::find($id);
        if ($request->action == 'status_toggle') {
            if ($countryName->status == 0) {
                $countryName->status = 1;
            } else {
                $countryName->status = 0;
            }
            $countryName->save();
            return redirect()->route('countries.index')
                ->with('success', 'Status has been changed successfully.');
        } else {
            $countryName = CountryName::find($id);
            $countryName->countryname = $request->countryname;
            if ($request->status == 'on') {
                $countryName->status = 1;
            } else {
                $countryName->status = 2;
            }
            $countryName->save();
            return redirect()->route('countries.index')
                ->with('success', 'Country Has Been updated successfully');
        }
    }

    public function destroy(Request $request)
    {
        $com = CountryName::where('id', $request->id)->delete();
        return response()->json(['success' => true, 'success' => 'Country deleted successfully']);
    }
}
