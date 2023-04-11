<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyController extends Controller
{
    public function index()
    {
        $data = Currency::all();
        return view('SuperAdmin/Pages/Currency/Index', compact('data'));
    }

    public function create()
    {
        return view('SuperAdmin/Pages/Currency/Create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'country_code' => 'required',
            'country_name' => 'required',
            'currency_symbol' => 'required',
        ]);

        $currency = new Currency;

        $currency->country_code = $request->country_code;
        $currency->country_name = $request->country_name;
        $currency->currency_symbol = $request->currency_symbol;
        $currency->value = $request->value;

        $defaultValue = Currency::all()->where('default', 1);

        if ($request->default == true && count($defaultValue) == 0) {
            $currency->default = 1;
        } else {
            $currency->default = 0;
        }

        if ($request->status == 'on') {
            $currency->status = 1;
        } else {
            $currency->status = 0;
        }
        $currency->save();
        return redirect()->route('Currency')
            ->with('success', 'New currency has been created successfully.');
    }

    public function edit($id)
    {
        $data =Currency::find($id);
        return view('SuperAdmin/Pages/Currency/Edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $currency = Currency::find($id);
        if ($request->action == 'status_toggle') {
            if ($currency->status == 0) {
                $currency->status = 1;
            } else {
                $currency->status = 0;
            }
            $currency->save();
            return redirect()->route('Currency')
                ->with('success', 'Currency status has been changed successfully.');
        } else {
            $currency->country_code = $request->country_code;
            $currency->country_name = $request->country_name;
            $currency->currency_symbol = $request->currency_symbol;
            $currency->value = $request->value;

            $defaultValue = Currency::all()->where('default', 1);

            if ($request->default == true && count($defaultValue) == 0) {
                $currency->default = 1;
            } else {
                $currency->default = 0;
            }
            if ($request->status == 'on') {
                $currency->status = 1;
            } else {
                $currency->status = 0;
            }
            $currency->save();
            return redirect()->route('Currency')
                ->with('success', 'Currency has been updated successfully.');
        }
    }

    public function show($id)
    {
        $data = Currency::where('id', $id)->get();
        return view('SuperAdmin/Pages/Currency/Show', compact('data'));
    }

    public function delete($id)
    {
        $data = Currency::where('id', $id)->delete();
        return redirect()->route('Currency')->with('success', 'Currency Has Been Deleted successfully');

    }
}
