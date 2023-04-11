<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Currency;


class CurrencyManagerController extends Controller
{
    public function index()
    {
        $defaultValue = Currency::all()->where('default', 1)->first();
        if (request()->ajax()) {
            return datatables()->of(Currency::select('*'))
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/currency.index', compact('defaultValue'));
    }
    public function create()
    {
        return view('admin/pages/currency.create');
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
        return redirect()->route('currencies.index')
            ->with('success', 'New currency has been created successfully.');
    }

    public function edit($id)
    {
        $currency = Currency::find($id);

        return view('admin/pages/currency.edit', compact('currency'));
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
            return redirect()->route('currencies.index')
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
            return redirect()->route('currencies.index')
                ->with('success', 'Currency has been updated successfully.');
        }
    }

    public function destroy(Request $request)
    {
        $com = Currency::where('id', $request->id)->delete();
        return response()->json(['success' => true, 'success' => 'Agent deleted successfully']);
    }
}
