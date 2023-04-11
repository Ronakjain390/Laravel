<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Banners;
use App\Models\News;
use App\Models\Reviewsratings;
use App\Models\Templates;
use App\Models\User;

class IndexController extends Controller
{

    public function adminHome()
    {

        $users = User::where('type', '0')->count();
        $agents = User::where('type', '2')->count();
        $banners = Banners::count();
        $templates = Templates::count();
        $news = News::count();
        $reviews = Reviewsratings::count();

        return view('admin/pages/index', compact('agents', 'users', 'banners', 'templates', 'news', 'reviews'));
    }
}
