<?php

namespace App\Http\Controllers\Frontend;

use App\Models\News;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsblogController extends Controller
{
    public function index()
    {
        $hotNews = News::latest()->take(5)->get();
        $data = News::where('status' ,1)->get(); 
        return view('user/pages/newsblog.index', compact('data','hotNews'));
    }

    public function newdetail($id)
    {
        $newsdata = News::get()->where('id', $id);
        return view('user/pages/newsdetail', compact('newsdata'));
    }
}
