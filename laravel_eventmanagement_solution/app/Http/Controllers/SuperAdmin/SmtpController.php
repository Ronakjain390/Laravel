<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmtpMail;

class SmtpController extends Controller
{
    public function index()
    {
        $data = SmtpMail::all();
        return view('SuperAdmin/Pages/Smtp/Index', compact('data'));
    }

    public function edit($id)
    {
        $data = SmtpMail::find($id);
        return view('SuperAdmin/Pages/Smtp/Edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = SmtpMail::find($id);
        $data->host = $request->host;
        $data->port = $request->port;
        $data->userName = $request->userName;
        $data->password = $request->password;
        $data->save();
        return redirect()->route('SmtpMail')
            ->with('success', 'Data Has Been updated successfully');
    }
}
