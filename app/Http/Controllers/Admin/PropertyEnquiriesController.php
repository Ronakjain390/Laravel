<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Propertyenquiry;
use Illuminate\Http\Request;

class PropertyEnquiriesController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Propertyenquiry::select('*'))
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin/pages/propertyenquiries.index');
    }
}
