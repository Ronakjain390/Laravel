<?php
    public function selectUser($invoiceSeries, $address, $email, $phone, $gst, $state, $pincode, $city, $receiver_name, $selectedUserDetails)
    {
        try {
            $this->userSelected = true;
            DB::beginTransaction();

            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $series = PanelSeriesNumber::where('user_id', $userId)
                ->where('default', "1")
                ->where('panel_id', '5')
                ->first();

            if (!$series) {
                throw new \Exception('No default series number found. Please add a default series number.');
            }

            $currentDate = now();
            $validTill = Carbon::parse($series->valid_till);

            if ($validTill->isPast()) {
                $this->errorMessage = 'Your series number has expired. Please choose an action:';
                $this->showSeriesExpirationModal = true;
                $this->expiredSeriesNumber = $invoiceSeries;

                // Store the user data temporarily
                $this->tempUserData = [
                    'invoiceSeries' => $invoiceSeries,
                    "invoiceNumber" => $invoiceNum,
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'pincode' => $pincode,
                    'email' => $email,
                    'phone' => $phone,
                    'gst' => $gst,
                    'buyer' => $buyer,
                    'selectedUserDetails' => $selectedUserDetails,
                ];

                DB::rollBack();
                return;
            }

            // If the series is not expired, proceed with the normal flow
            $this->invoiceNumber($invoiceSeries, $address, $email, $phone, $gst, $state, $pincode, $city, $receiver_name, $selectedUserDetails);

            // Fetch billTo data
            $this->fetchBillToData();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in selectUser method: ' . $e->getMessage());
            $this->errorMessage = 'An error occurred while processing your request: ' . $e->getMessage();
            return;
        }
    }
  public function invoiceNumber($invoiceSeries, $address, $email, $phone, $gst, $state, $pincode, $city, $receiver_name, $selectedUserDetails)
    {
        if ($invoiceSeries == 'Not Assigned') {
            if ($series == null) {
                throw new \Exception('Please add one default Series number');
            }
            $invoiceSeries = $series->series_number;
        } else {
            // Check if the selected series is valid and not expired
            $selectedSeries = PanelSeriesNumber::where('user_id', $userId)
                ->where('series_number', $invoiceSeries)
                ->where('panel_id', '5')
                ->first();

            if (!$selectedSeries) {
                throw new \Exception('Invalid series number selected.');
            }

            $validTill = Carbon::parse($selectedSeries->valid_till);
            if ($validTill->isPast()) {
                $this->errorMessage = 'Your series number has expired. Please choose an action:';
                $this->showSeriesExpirationModal = true;
                $this->expiredSeriesNumber = $invoiceSeries;
                // Store the user data temporarily
                $this->tempUserData = [
                    'invoiceSeries' => $invoiceSeries,
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'pincode' => $pincode,
                    'email' => $email,
                    'phone' => $phone,
                    'gst' => $gst,
                    'buyer' => $buyer,
                    'selectedUserDetails' => $selectedUserDetails,
                ];
                // dd($invoiceSeries, $address, $email, $phone, $gst, $state, $pincode, $city, $receiver_name, $selectedUserDetails);
                return;
            }
        }

        $latestSeriesNum = GoodsReceipt::where('goods_series', $invoiceSeries)
                    ->where('seller_id', $userId)
                    ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

        $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

        $this->selectedUser = [
            "invoiceSeries" => $invoiceSeries,
            "invoiceNumber" => $seriesNum,
            "address" => $address,
            "receiver_name" => $buyer,
            "email" => $email,
            "phone" => $phone,
            "gst" => $gst,
            "city" => $city,
            "state" => $state,
            "pincode" => $pincode,
        ];

        $decodedUserDetails = json_decode($selectedUserDetails);
        $this->receiverName = $this->selectedUser['receiver_name'];
        $this->create_invoice_request['goods_series'] = $invoiceSeries;
        $this->create_invoice_request['series_num'] = $seriesNum;
        $this->create_invoice_request['buyer'] = $buyer;
        $this->create_invoice_request['buyer_id'] = $decodedUserDetails->buyer_user_id;
        $this->create_invoice_request['feature_id'] = 1;
        $this->selectedUserDetails = $decodedUserDetails->user->details;
    }

    public function useDefaultSeries()
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $defaultSeries = PanelSeriesNumber::where('user_id', $userId)
            ->where('default', "1")
            ->where('panel_id', '5')
            ->first();

        if (!$defaultSeries) {
            $this->errorMessage = 'No default series number found. Please add a default series number.';
            return;
        }

        $validTill = Carbon::parse($defaultSeries->valid_till);
        if ($validTill->isPast()) {
            $this->errorMessage = 'The default series number has also expired. Please update your series numbers.';
            return;
        }

        // Use the default series number
        $this->selectedUser['invoiceSeries'] = $defaultSeries->series_number;
        $this->create_invoice_request['goods_series'] = $defaultSeries->series_number;

        // Recalculate the series number

        $latestSeriesNum = GoodsReceipt::where('goods_series', $defaultSeries->series_number)
            ->where('seller_id', $userId)
            ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

        $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
        $this->selectedUser['invoiceNumber'] = $seriesNum;
        $this->create_invoice_request['series_num'] = $seriesNum;

        $this->showSeriesExpirationModal = false;
        $this->errorMessage = null;
    }

    private function fetchBillToData()
    {
        try {
            $request = request();
            $billTo = new ReceiverGoodsReceiptsController;
            $this->billTo = $billTo->index($request)->getData()->data;
            if ($this->billTo === null) {
                // Handle error: invalid JSON or empty response
                $this->billTo = [];
            } else {
                $this->billTo = collect($this->billTo)
                    ->filter(function ($item) {
                        return !empty($item->receiver_name) || !empty($item->details[0]->phone) || !empty($item->details[0]->email);
                    })
                    ->map(function ($item) {
                        $buyerName = !empty($item->receiver_name) ? $item->receiver_name : (!empty($item->details[0]->phone) ? $item->details[0]->phone : $item->details[0]->email);
                        return (object) array_merge((array) $item, ['receiver_name' => $buyerName]);
                    })
                    ->sortBy(function ($item) {
                        $buyerName = strtolower($item->receiver_name);
                        return is_numeric($buyerName[0]) ? 'z' . $buyerName : $buyerName;
                    })
                    ->values()
                    ->all();
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching billTo data: ' . $e->getMessage());
            $this->billTo = [];
        }
    }

    public function useDefaultSeriesNumber()
    {
        try {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            // Fetch the default series
            $defaultSeries = PanelSeriesNumber::where('user_id', $userId)
                ->where('default', "1")
                ->where('panel_id', '5')
                ->first();

            if (!$defaultSeries) {
                $this->errorMessage = 'No default series number found. Please add a default series number.';
                return;
            }

            // Check if the default series is valid
            $validTill = Carbon::parse($defaultSeries->valid_till);
            if ($validTill->isPast()) {
                $this->errorMessage = 'The default series number has expired. Please update your series numbers.';
                return;
            }

            // Use the stored temporary data
            if (!empty($this->tempUserData)) {
                $this->invoiceNumber(
                    $defaultSeries->series_number,
                    $this->tempUserData['address'],
                    $this->tempUserData['city'],
                    $this->tempUserData['state'],
                    $this->tempUserData['pincode'],
                    $this->tempUserData['email'],
                    $this->tempUserData['phone'],
                    $this->tempUserData['gst'],
                    $this->tempUserData['buyer'],
                    $this->tempUserData['selectedUserDetails'],
                    $userId,
                    $defaultSeries
                );

                // Clear the temporary data
                $this->tempUserData = [];
            } else {
                $this->errorMessage = 'User data not found. Please try selecting the user again.';
                return;
            }

            // Fetch BillTo data
            $this->fetchBillToData();

            // Reset error message and close modal
            $this->showSeriesExpirationModal = false;
            $this->errorMessage = null;

        } catch (\Exception $e) {
            \Log::error('Error in useDefaultSeriesNumber method: ' . $e->getMessage());
            $this->errorMessage = 'An error occurred while using the default series number: ' . $e->getMessage();
        }
    }


    protected function getListeners()
    {
        return array_merge(parent::getListeners(), [
            'openUpdateSeriesModal' => 'openUpdateSeriesModal',
        ]);
    }

    public function openUpdateSeriesModal()
    {
        return redirect()->route('sender', ['template' => 'challan_series_no']);
        $this->showSeriesExpirationModal = false;
        $this->showUpdateSeriesModal = true;
    }

    public function closeUpdateSeriesModal()
    {
        $this->showUpdateSeriesModal = false;
    }
