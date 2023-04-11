<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\User; 

class AgentFrontController extends Controller
{
    public function index()
    {
        $data = User::all()->where('type', 2)->where('status', 1);
        return view('user/pages/agent', compact('data'));
    }
}
