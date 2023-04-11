<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;

class TemplateController extends Controller
{
    public function index()
    {
        $data = Template::all();
        return view('SuperAdmin/Pages/Template/Index', compact('data'));
    }

    public function create()
    {
        return view('SuperAdmin/Pages/Template/Create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'subject' => 'required',
            'content' => 'required'
        ]);
        $template = new Template;
        $template->name = $request->name;
        $template->subject = $request->subject;
        $template->content = $request->content;
        if ($request->status == 'on') {
            $template->status = 1;
        } else {
            $template->status = 0;
        }
        $template->save();
        return redirect()->route('Templates')
            ->with('success', 'Template has been created successfully.');
    }

    public function edit($id)
    {
        $data = Template::find($id);
        return view('SuperAdmin/Pages/Template/Edit', compact('data'));
    }

    public function show($id)
    {
        $data =Template::where('id', $id)->get();
        return view('SuperAdmin/Pages/Template/Show', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $template = Template::find($id);
        if ($request->action == 'status_toggle') {
            if ($template->status == 0) {
                $template->status = 1;
            } else {
                $template->status = 0;
            }
            $template->save();
            return redirect()->route('Templates')
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
            return redirect()->route('Templates')
                ->with('success', 'Template Has Been updated successfully');
        }
    }

    public function delete($id)
    {
        $del =Template::where('id', $id)->delete();
        return redirect()->route('Templates')->with('error', 'Template Has Been Deleted successfully');
    }
}
