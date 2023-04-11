<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $data = Faq::all();
        return view('SuperAdmin/Pages/Faq/Index', compact('data'));
    }

    public function create()
    {
        return view('SuperAdmin/Pages/Faq/Create');
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
        return redirect()->route('Faqs')
            ->with('success', 'Faq has been created successfully.');
    }

    public function edit($id)
    {
        $data = Faq::find($id);
        return view('SuperAdmin/Pages/Faq/Edit', compact('data'));
    }

    public function show($id)
    {
        $data =Faq::where('id', $id)->get();
        return view('SuperAdmin/Pages/Faq/Show', compact('data'));
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
            return redirect()->route('Faqs')
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
            return redirect()->route('Faqs')
                ->with('success', 'Faq Has Been updated successfully');
        }
    }

    public function delete($id)
    {
        $del =Faq::where('id', $id)->delete();
        return redirect()->route('Faqs')->with('error', 'Faq Has Been Deleted successfully');
    }
}
