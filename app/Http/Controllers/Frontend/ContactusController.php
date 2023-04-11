<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Contact;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactusController extends Controller
{
    public function index()
    {
        return view('user/pages/contactus.create');
    }

    // Store 
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'contact_details' => 'required',
            'subject' => 'required',
            'description' => 'required',
        ]);

        $contact = new Contact;
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->contact_details = $request->contact_details;
        $contact->subject = $request->subject;
        $contact->description = $request->description;
        $contact->save();
        return redirect()->route('home')->with('success', 'Thanks for your feedback, we will connect you soon.');
    }
}
