<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\SendNewsletter;
use App\Models\Newsletter;
use App\Models\SubscribedUser;
use App\Models\SentTo;

use Illuminate\Http\Request;
use Datatbales;

class SendNewsLetterController extends Controller
{

    public function index()
    {
        $temp = Newsletter::all();
        $sub = SubscribedUser::all();
        return view('admin/pages/sendnewsletter.index', compact(['temp', 'sub']));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'template' => 'required',
            'subscribed_user' => 'required'
        ]);
        $x = $request->subscribed_user;
        foreach ($x as $s) {

            $sendnewsletter = new SendNewsletter();
            $sendnewsletter->title = $request->title;
            $sendnewsletter->template = $request->template;
            $sendnewsletter->subscribed_user = $s;
            $sendnewsletter->save();
        }

        $temp = $sendnewsletter->template;
        $title = $sendnewsletter->title;
        $sub = $sendnewsletter->subscribed_user;
        $sendto = new SentTo();
        $sendto->title = $title;
        $sendto->templateid = $temp;
        $sendto->subscribedid = $sub;
        $sendto->save();
        return redirect()->route('send-newsletters.index')
            ->with('success', 'Newsletter has been sent successfully.');
    }
}
