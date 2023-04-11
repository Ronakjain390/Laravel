<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\SmtpMail;

use Illuminate\Http\Request;

class SmtpMailController extends Controller
{
        public function index()
        {
                $data = SmtpMail::all();

                return view('admin/pages/smtpmail.index', compact('data'));
        }
        public function edit($id)
        {
                $data = SmtpMail::find($id);

                return view('admin/pages/smtpmail.edit', compact('data'));
        }
        public function update(Request $request, $id)
        {
                $data = SmtpMail::find($id);
                $data->host = $request->host;
                $data->port = $request->port;
                $data->userName = $request->userName;
                $data->password = $request->password;
                $data->save();
                return redirect()->route('smtpmail.index')
                        ->with('success', 'Data Has Been updated successfully');
        }
}
