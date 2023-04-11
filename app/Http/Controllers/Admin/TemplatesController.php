<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\Templates;

use Illuminate\Http\Request;
use Datatables;
use GuzzleHttp\Middleware;
use SebastianBergmann\Template\Template;

class TemplatesController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Templates::select('*'))
                ->rawColumns(['action'])
                ->escapeColumns('aaData')
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/templates.index');
    }

    public function create()
    {
        return view('admin/pages/templates.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'subject' => 'required',
            'content' => 'required'
        ]);
        $template = new Templates;
        $template->name = $request->name;
        $template->subject = $request->subject;
        $template->content = $request->content;
        if ($request->status == 'on') {
            $template->status = 1;
        } else {
            $template->status = 0;
        }
        $template->save();
        return redirect()->route('templates.index')
            ->with('success', 'Template has been created successfully.');
    }

    public function show(Templates $template)
    {
        return view('admin/pages/templates.show', compact('template'));
    }

    public function edit(Templates $template)
    {
        return view('admin/pages/templates.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = Templates::find($id);
        if ($request->action == 'status_toggle') {
            if ($template->status == 0) {
                $template->status = 1;
            } else {
                $template->status = 0;
            }
            $template->save();
            return redirect()->route('templates.index')
                ->with('success', 'Status has been changed successfully.');
        } else {
            $template->name = $request->name;
            $template->subject = $request->subject;
            $template->content = $request->content;
            if ($request->status == 'on') {
                $template->status = 1;
            } else {
                $template->status = 0;
            }
            $template->save();
            return redirect()->route('templates.index')
                ->with('success', 'Template Has Been updated successfully');
        }
    }

    public function destroy(Request $request)
    {
        $com = Templates::where('id', $request->id)->delete();
        return response()->json(['delete' => true, 'delete' => 'Article deleted successfully']);
    }
}
