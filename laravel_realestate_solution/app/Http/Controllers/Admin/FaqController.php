<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\Faq;
use Illuminate\Http\Request;
use Datatables;

class FaqController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Faq::select('*'))
                ->rawColumns(['action'])
                ->escapeColumns('aaData')
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/faqs.index');
    }

    public function create()
    {
        return view('admin/pages/faqs.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'question' => 'required',
            'answer' => 'required'
        ]);

        $faq = new Faq;
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        if ($request->status == 'on') {
            $faq->status = 1;
        } else {
            $faq->status = 0;
        }
        $faq->save();
        return redirect()->route('faqs.index')
            ->with('success', 'Faq has been created successfully.');
    }

    public function show(Faq $faq)
    {
        return view('admin/pages/faqs.show', compact('faq'));
    }

    public function edit(Faq $faq)
    {
        return view('admin/pages/faqs.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::find($id);
        if ($request->action == 'status_toggle') {
            if ($faq->status == 0) {
                $faq->status = 1;
            } else {
                $faq->status = 0;
            }
            $faq->save();
            return redirect()->route('faqs.index')
                ->with('success', 'User status has been changed successfully.');
        } else {
            $faq->question = $request->question;
            $faq->answer = $request->answer;
            if ($request->status == 'on') {
                $faq->status = 1;
            } else {
                $faq->status = 0;
            }
            $faq->save();
            return redirect()->route('faqs.index')
                ->with('success', 'Faq Has Been updated successfully');
        }
    }

    public function destroy(Request $request)
    {
        $com = Faq::where('id', $request->id)->delete();
        return response()->json(['success' => true, 'success' => 'Article deleted successfully']);
    }
}
