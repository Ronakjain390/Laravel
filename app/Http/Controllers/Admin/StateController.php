<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\State;
use App\Models\CountryName;

class StateController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(State::select('*'))
                ->rawColumns(['action'])
                ->escapeColumns('aaData')
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/states.index');
    }

    public function create()
    {
        $countries = CountryName::where('status', 1)->get();
        return view('admin/pages/states.create', compact('countries'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'statename' => 'required',

        ]);

        $states = new State;
        $states->statename = $request->statename;
        $states->country = $request->countryname;
        if ($request->status == 'on') {
            $states->status = 1;
        } else {
            $states->status = 0;
        }
        $states->save();
        return redirect()->route('states.index')
            ->with('success', 'State has been created successfully.');
    }

    public function show($id)
    {
        $states = State::find($id);
        $country = CountryName::where('id', $states->country )->get();
        return view('admin/pages/states.show', compact('states', 'country'));
    }

    public function edit($id)
    {
        $states = State::find($id);
        $country = CountryName::all();
        return view('admin/pages/states.edit', compact('states', 'country'));
    }

    public function update(Request $request, $id)
    {

        $states = State::find($id);
        if ($request->action == 'status_toggle') {
            if ($states->status == 0) {
                $states->status = 1;
            } else {
                $states->status = 0;
            }
            $states->save();
            return redirect()->route('states.index')
                ->with('success', 'Status has been changed successfully.');
        } else {

            $states->statename = $request->statename;
            $states->country = $request->countryname;
            if ($request->status == 'on') {
                $states->status = 1;
            } else {
                $states->status = 0;
            }
            $states->save();
            return redirect()->route('states.index')
                ->with('success', 'State Has Been updated successfully');
        }
    }

    public function destroy(Request $request)
    {
        $com = State::where('id', $request->id)->delete();
        return response()->json(['success' => true, 'success' => 'state has been deleted successfully']);
    }
}
