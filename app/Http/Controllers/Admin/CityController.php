<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CountryName;
use App\Models\State;
use App\Models\Cityname;

class CityController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Cityname::select('*'))
                ->rawColumns(['action'])
                ->escapeColumns('aaData')
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/cities.index');
    }

    public function create()
    {
        $country = CountryName::where('status', 1)->get();
        $states = State::where('status', 1)->get();
        return view('admin/pages/cities.create', compact('country', 'states'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'city' => 'required',
            'countryid' => 'required',
            'stateid' => 'required',
        ]);

        $city = new Cityname();
        $city->countryid = $request->countryid;
        $city->stateid = $request->stateid;
        $city->city = $request->city;

        if ($request->status == 'on') {
            $city->status = 1;
        } else {
            $city->status = 0;
        }
        $city->save();
        return redirect()->route('cities.index')
            ->with('success', 'City has been created successfully.');
    }

    public function show($id)
    {
        $city = Cityname::find($id);
        $state = State::where('id', $city->stateid)->get('statename');
        $country = CountryName::where('id', $city->countryid)->get('countryname');
        return view('admin/pages/cities.show', compact('city', 'state', 'country'));
    }

    public function edit($id)
    {
        $city = Cityname::find($id);
        $state = State::all();
        $country = CountryName::all();
        return view('admin/pages/cities.edit', compact('city', 'state', 'country'));
    }

    public function update(Request $request, $id)
    {

        $city = Cityname::find($id);
        if ($request->action == 'status_toggle') {
            if ($city->status == 0) {
                $city->status = 1;
            } else {
                $city->status = 0;
            }
            $city->save();
            return redirect()->route('cities.index')
                ->with('success', 'Status has been changed successfully.');
        } else {

            $city->stateid = $request->stateid;
            $city->countryid = $request->countryid;
            $city->city = $request->city;
            if ($request->status == 'on') {
                $city->status = 1;
            } else {
                $city->status = 0;
            }
            $city->save();
            return redirect()->route('cities.index')
                ->with('success', 'City Has Been updated successfully');
        }
    }

    public function destroy(Request $request)
    {
        $com = Cityname::where('id', $request->id)->delete();
        return response()->json(['success' => true, 'success' => 'City has been deleted successfully']);
    }
}
