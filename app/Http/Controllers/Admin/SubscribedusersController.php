<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscribedUser;

class SubscribedusersController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(SubscribedUser::select('*'))
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/subscribedusers.index');
    }
}
