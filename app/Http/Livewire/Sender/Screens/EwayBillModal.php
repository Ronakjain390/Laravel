<?php

namespace App\Http\Livewire\Sender\Screens;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Models\Invoice;
use Carbon\Carbon;

use Livewire\Component;

class EwayBillModal extends Component
{
    public $showModal = false;
    public $columnId;
    public $email = 'jainronak390@gmail.com';
    public $username;
    public $password;
    public $client_id = 'e69d0f45-07c9-41d2-992b-5fc06c99fd83';
    public $client_secret = '39283097-3e2f-4e15-b1a8-d55a973407b7';
    public $gstin;
    public $ip_address = '223.185.12.194';
    public $loginSuccess = false;
    public $remember = false;
    public $cookiesPresent = false;
    public $createEwayBill = false;
    public $errorMessage = '';

    protected $listeners = ['showEwayBillModal', 'cancelEwayBill'];

    public function mount()
    {
        // Check if the user has credentials stored in cookies
        if (Cookie::has('ewaybill_username')) {
            // dd('here');
            $this->username = Cookie::get('ewaybill_username');
            $this->password = Cookie::get('ewaybill_password');
            $this->gstin = Cookie::get('ewaybill_gstin');
            $this->cookiesPresent = true;
        }
    }

    protected $rules = [
        'username' => 'required|string|min:3',
        'password' => 'required|string|min:6',
        'gstin' => 'required|string|size:15',
    ];

    protected $messages = [
        'username.required' => 'Username is required.',
        'username.min' => 'Username must be at least 3 characters.',
        'password.required' => 'Password is required.',
        'password.min' => 'Password must be at least 6 characters.',
        'gstin.required' => 'GSTIN is required.',
        'gstin.size' => 'GSTIN must be exactly 15 characters.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function fetchIpAddress()
    {
        $request = request();
        // $this->ip_address = $request->header('X-Forwarded-For') ?? $request->ip();
        // dd($this->ip_address);
    }

    public function login()
    {
        $this->validate();

        $url = 'https://api.mastergst.com/ewaybillapi/v1.03/authenticate?' . http_build_query([
            'email' => $this->email,
            'username' => $this->username,
            'password' => $this->password,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'gstin' => $this->gstin,
            'ip_address' => $this->ip_address,
        ]);

        try {
            $response = Http::withHeaders([
                'ip_address' => $this->ip_address,
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'gstin' => $this->gstin,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->withoutVerifying()->get($url);

            $responseData = $response->json();
            if ($response->ok() && isset($responseData['status_cd']) && $responseData['status_cd'] === '1') {
                $this->loginSuccess = true;
                if ($this->remember) {
                    Cookie::queue('ewaybill_username', $this->username, 1440);
                    Cookie::queue('ewaybill_password', $this->password, 1440);
                    Cookie::queue('ewaybill_gstin', $this->gstin, 1440);
                }
                session(['columnId' => $this->columnId]);
                session(['ewaybill_token' => $responseData['header'] ?? []]);
                session(['ewaybill_login_time' => now()]);
                session(['gstin' => $this->gstin]);

                // Store remaining details for 5 hours
                $expirationTime = now()->addHours(5);
                session([
                    'ewaybill_email' => $this->email,
                    'ewaybill_client_id' => $this->client_id,
                    'ewaybill_client_secret' => $this->client_secret,
                    'ewaybill_ip_address' => $this->ip_address,
                    'ewaybill_expiration' => $expirationTime
                ]);

                $this->closeModal();

                $successMessage = 'Login successful';
                session()->flash('message', $successMessage);

                return redirect()->route('seller', ['template' => 'create_e_way_bill']);
            } else {
                // Authentication failed
                $errorCode = $responseData['error']['error_cd'] ?? '';
                $errorMessage = $this->getErrorMessage($errorCode);
                $this->errorMessage = 'Login failed: ' . $errorMessage;
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Request failed: ' . $e->getMessage();
        }
    }

    public function showEwayBillModal($id)
    {
        $lastLoginTime = session('ewaybill_login_time');
        $currentTime = now();

        if ($lastLoginTime && $currentTime->diffInHours($lastLoginTime) < 5) {
            // If less than 5 hours have passed since last login, redirect directly
            return redirect()->route('seller', ['template' => 'create_e_way_bill']);
        }

        $this->columnId = $id;
        $this->showModal = true;
    }

    public function cancelEwayBill($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $url = 'https://api.mastergst.com/ewaybillapi/v1.03/ewayapi/canewb';

        // Check if the session data is still valid
        $expirationTime = session('ewaybill_expiration');
        if (!$expirationTime || now()->isAfter($expirationTime)) {
            $this->addError('session', 'Your session has expired. Please log in again.');
            $this->showModal = true;
            return;
        }

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'ip_address: ' . session('ewaybill_ip_address'),
            'client_id: ' . session('ewaybill_client_id'),
            'client_secret: ' . session('ewaybill_client_secret'),
            'gstin: ' . session('gstin'),
        ];

        $data = [
            'ewbNo' => $invoice->eway_bill_no,
            'cancelRsnCode' => 2,
            'cancelRmrk' => 'Order Cancelled'
        ];

        $jsonData = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?email=' . urlencode(session('ewaybill_email')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            $this->addError('curl', 'cURL error: ' . $error_msg);
            return;
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($responseData['status_cd'] === '1') {
            // Success
            $invoice->eway_bill_pdf_url = null;
            $invoice->eway_bill_status = 'cancelled';

            $invoice->save();
            $this->dispatchBrowserEvent('show-success-message', ['E-way Bill cancelled successfully.']);
            // $this->emit('ewayBillCancelled', 'E-way Bill cancelled successfully.');
            $this->closeModal();
        } else {
            // Error
            $errorMessage = $responseData['error']['message'] ?? 'Unknown error';
            $this->addError('api', 'Failed to cancel E-way Bill: ' . $errorMessage);
        }
    }

    private function getErrorMessage($errorCode)
    {
        $errorListUrl = 'https://api.mastergst.com/ewaybillapi/v1.03/ewayapi/geterrorlist';

        try {
            $response = Http::withHeaders([
                'ip_address' => $this->ip_address,
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'gstin' => $this->gstin,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->withoutVerifying()->get($errorListUrl);

            $errorList = $response;
            // dd($errorList);
            if (isset($errorList['data'])) {
                $errors = json_decode($errorList['data'], true);
                foreach ($errors as $error) {
                    if ($error['error_cd'] === $errorCode) {
                        return $error['desc'];
                    }
                }
            }
        } catch (\Exception $e) {
            // If there's an error fetching the error list, return a generic message
            return 'Invalid Credentials.';
        }

        // If the error code is not found in the list, return a generic message
        return 'Invalid Credentials.';
    }

    private function resetInputFields()
    {
        $this->email = '';
        $this->username = '';
        $this->password = '';
        $this->client_id = '';
        $this->client_secret = '';
        $this->gstin = '';
        $this->ip_address = '';
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->loginSuccess = false;
        $this->errorMessage = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.sender.screens.eway-bill-modal');
    }
}
