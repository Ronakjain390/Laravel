<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Database\Factories\SitemapFactory;
use Illuminate\Http\Request;
use App\Models\Sitemap;
class SitemapXmlController extends Controller
{
    public function index() {
        $posts = Sitemap::all();
        return response()->view('user/pages/sitemap/index', [
            'posts' => $posts
        ])->header('Content-Type', 'text/xml');
      }
}
