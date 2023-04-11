<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\SentTo;

use Illuminate\Http\Request;

class MailSentToController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(SentTo::select('*'))
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/sentto.index');
    }
}
