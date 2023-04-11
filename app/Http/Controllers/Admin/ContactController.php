<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\Contact;

class ContactController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Contact::select('*'))
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin/pages/contacts.index');
    }

    public function show($id){
        $data = Contact::find($id);
        return view('admin/pages/contacts/show', compact('data'));
    }
}
