<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactEnquiry;

class ContactEnquiryController extends Controller
{
    public function index()
    {
        $data = ContactEnquiry::all();
        return view('SuperAdmin/Pages/ContactEnquiry/Index', compact('data'));
    }

    public function show($id)
    {
        $data =contactEnquiry::where('id', $id)->get();
        return view('SuperAdmin/Pages/ContactEnquiry/Show', compact('data'));
    }
}
