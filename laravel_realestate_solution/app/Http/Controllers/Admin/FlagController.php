<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flags;

class FlagController extends Controller
{
    public function index()
    {
        $data = Flags::all();
        return view('admin/pages/flags.index', compact('data'));
    }

    public function create()
    {
        return view('admin/pages/flags.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'flag_name' => 'required',
        ]);
        $flags = new Flags;
        $flags->flag_name = $request->flag_name;
        $flags->save();
        return redirect()->route('Flags')
            ->with('success', 'Property Category has been created successfully.');
    }

    public function destroy($id)
    {
        $com = Flags::where('id', $id)->delete();
        return redirect()->route('Flags')->with('success', 'Flag has been deleted succesfully...');
    }
}
