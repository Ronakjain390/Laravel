<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Newsletter;

use Illuminate\Http\Request;
use Datatables;

class NewsLetterController extends Controller
{
    //
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Newsletter::select('*'))
                ->rawColumns(['action'])
                ->escapeColumns('aaData')
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/newsletters.index');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin/pages/newsletters.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'subject' => 'required',
            'content' => 'required'
        ]);
        $newsletter = new Newsletter();
        $newsletter->name = $request->name;
        $newsletter->subject = $request->subject;
        $newsletter->content = $request->content;
        $newsletter->save();
        return redirect()->route('newsletters.index')
            ->with('success', 'Newsletter has been created successfully.');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Newsletter $newsletter)
    {
        return view('admin/pages/newsletters.edit', compact('newsletter'));
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Newsletter $newsletter)
    {
        return view('admin/pages/newsletters.show', compact('newsletter'));
    }
    public function update(Request $request, $id)
    {
        $latters = Newsletter::find($id);
        if ($request->action == 'status_toggle') {
            if ($latters->status == 0) {
                $latters->status = 1;
            } else {
                $latters->status = 0;
            }
            $latters->save();
            return redirect()->route('newsletters.index')
                ->with('success', 'Status has been changed successfully.');
        } else {
            $request->validate([

                'name' => 'required',
                'subject' => 'required',
                'content' => 'required'
            ]);
            $newsletter = Newsletter::find($id);
            $newsletter->name = $request->name;
            $newsletter->subject = $request->subject;
            $newsletter->content = $request->content;
            if ($request->status == 'on') {
                $newsletter->status = 'Active';
            } else {
                $newsletter->status = 'InActive';
            }
            $newsletter->save();
            return redirect()->route('newsletters.index')
                ->with('success', 'Newsletter Has Been updated successfully');
        }
    }
    public function destroy(Request $request)
    {
        $com = Newsletter::where('id', $request->id)->delete();
        return response()->json(['success' => true, 'success' => 'Newsletter deleted successfully']);
    }
}
