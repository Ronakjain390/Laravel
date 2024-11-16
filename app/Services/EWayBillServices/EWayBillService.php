<?php
namespace App\Services\EWayBillServices;

use Illuminate\Support\Facades\Http;

class EWayBillService
{
    protected $baseUrl = 'https://api.mastergst.com/ewaybillapi/v1.03';

    public function authenticate()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'ip_address' => '152.59.101.65',
            'client_id' => 'e69d0f45-07c9-41d2-992b-5fc06c99fd83',
            'client_secret' => '39283097-3e2f-4e15-b1a8-d55a973407b7',
            'gstin' => '05AAACH6188F1ZM',
        ])->get("{$this->baseUrl}/authenticate", [
            'email' => 'jainro@gmail.com',
            'username' => '6188F1ZM',
            'password' => 'abc123@@'
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}