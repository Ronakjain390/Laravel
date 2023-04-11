<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MailSentUser;

class MailsentController extends Controller
{
    public function index()
    {
        $data = MailSentUser::all();
        return view('SuperAdmin/Pages/MailSentUsers/Index', compact('data'));
    }
}
