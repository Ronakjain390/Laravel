<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqsController extends Controller
{
    public function index()
    {
        $data = Faq::where('status' ,1)->get();
        return view('User/pages/Faq/Index', compact('data'));
    }
}
