<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\News;
use Illuminate\Http\Request;
use Datatables;

class NewsController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(News::select('*'))
                ->rawColumns(['action'])
                ->escapeColumns('aaData')
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/news.index');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin/pages/news.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',

        ]);


        $file_name = time() . '.' . request()->file('image')->getClientOriginalExtension();

        request()->image->move(public_path('assets/images/news/'), $file_name);


        $news = new News;
        $news->title = $request->title;
        $news->description = $request->description;
        $news->image = $file_name;
        if ($request->status == 'on') {
            $news->status = 1;
        } else {
            $news->status = 0;
        }
        $news->save();
        return redirect()->route('news.index')
            ->with('success', 'News has been created successfully.');
    }
    /**
     * Display the specified resource.
     *
     * @param \App\news $company
     * @return \Illuminate\Http\Response
     */
    public function show(News $news)
    {
        return view('admin/pages/news.show', compact('news'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\News $company
     * @return \Illuminate\Http\Response
     */
    public function edit(News $news)
    {
        return view('admin/pages/news.edit', compact('news'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\news $news
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $news = News::find($id);
        if ($request->action == 'status_toggle') {
            if ($news->status == 0) {
                $news->status = 1;
            } else {
                $news->status = 0;
            }
            $news->save();
            return redirect()->route('news.index')
                ->with('success', 'Status has been changed successfully.');
        } else {
            $Image = $request->hidden_Image;

            if ($request->image != '') {
                $Image = time() . '.' . request()->image->getClientOriginalExtension();

                request()->image->move(public_path('assets/images/news/'), $Image);
            }
            $news->title = $request->title;
            $news->description = $request->description;
            $news->image = $Image;
            if ($request->status == 'on') {
                $news->status = 1;
            } else {
                $news->status = 0;
            }
            $news->save();
            return redirect()->route('news.index')
                ->with('success', 'News Has Been updated successfully');
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param \App\News $news
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $com = News::where('id', $request->id)->delete();
        return response()->json(['success' => true, 'success' => 'Article deleted successfully']);
    }
}
