<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Frontend\SendNewsletter;
use App\Models\SubscribedUser;
use Illuminate\Http\Request;

class SubscribeduserController extends Controller
{
    
    public function store(Request $request)
    {

        $this->validate($request, [
            'email' => 'required',
        ]);

        $subscribeduser = new SubscribedUser();
        $subscribeduser->email = $request->email;
        $subscribeduser->save();
        return redirect()->back()->with('emailSubscribe', 'You have Subscribed Successfully');
    }
}
