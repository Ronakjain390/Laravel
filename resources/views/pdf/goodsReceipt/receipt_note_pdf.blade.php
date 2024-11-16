    <!DOCTYPE html>
    <html>

    <head>
        <title> Receipt Note</title>
        {{-- @dd($goodsReceipt); --}}
        <style>
            .invoice-box {
                max-width: 890px;
                margin: auto;
                padding: 2px;
                box-shadow: 0 0 10px rgba(0, 0, 0, .15);
                font-size: 10px;
                line-height: 14px;
                font-family: 'Helvetica Neue', 'DejaVu Sans', 'Helvetica', Helvetica, Arial, sans-serif;
                color: #000;
                 margin-bottom: 100px;
            }

            .invoice-box table {
                width: 100%;
                line-height: inherit;
                text-align: left;
            }

            .invoice-box table td {
                /* padding: 5px; */
                vertical-align: top;
            }

            .invoice-box table tr td:nth-child(2),
            .text-right {
                /* text-align: right; */
            }

            .invoice-box table tr.top table td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.top table td.title {
                font-size: 45px;
                line-height: 45px;
                color: #333;
            }

            .invoice-box table tr.information table td {
                padding-bottom: 20px;
                font-size: smaller;
                line-height: 12px;

            }

            .invoice-box table tr.heading td {
                background: black;
                color: white;
                margin: 0;
                font-size: 12px;
            }

            .invoice-box table tr.details td {
                padding-bottom: 20px;
            }

            .invoice-box table {
                width: 100%;
                line-height: inherit;
                text-align: left;
                border-collapse: collapse;
                /* Add this line to remove spacing between cells */
            }

            .invoice-box table td {
                /* padding: 5px; */
                vertical-align: top;
            }


            .invoice-box table tr.item.last td {
                border-bottom: none;
            }

            .invoice-box table tr.total td:nth-child(2) {
                /* border-top: 2px solid #eee; */
                font-weight: bold;
            }

            .item td {
                /* width: 10%; */
                /* text-align: center; */
                white-space: nowrap;
            }

            .heading td {
                white-space: nowrap;
            }

            .total {
                border-top: 1px solid #000;
                border-bottom: 1px solid #000;
                text-align: left;
            }

            td b {
                display: inline;
            }

            td p {
                display: inline;
                margin-left: 10px;
                /* Add margin for spacing between <b> and <p> */
            }

            .footer {
                position: fixed;
                width: 100%;
                text-align: center;
                color: black;
                line-height: 8px;
                bottom: 0;
            }


            .footer img {
                height: 35px;
                margin: 8px;
                margin-bottom: 10px;
            }

            .header_logo img {
                height: 100px;
                width: 700px;
                /* margin: 8px; */
                /* margin-bottom: 10px; */
                /* border: 1px solid #ccc; */
                /* padding: 10px; */
            }

            /* .header_logo img{
                width: 100%;
                height: auto;
                display: block;
            } */

            .center-align {
                text-align: center;
            }

            .left-align {
                text-align: left;
            }

            .right-align {
                text-align: right;
            }


            @media only screen and (max-width: 600px) {
                .invoice-box table tr.top table td {
                    width: 100%;
                    display: block;
                    /* text-align: center; */
                }

                .invoice-box table tr.information table td {
                    width: 100%;
                    display: block;
                    /* text-align: center; */

                }
            }

            .rtl {
                direction: rtl;
                font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            }

            .rtl table {
                /* text-align: right; */
            }

            .rtl table tr td:nth-child(2) {
                text-align: left;
            }
        </style>
    </head>

    <body>
        @php
        if (!function_exists('convertNumberToWords')) {
        function convertNumberToWords($number)
        {
            $words = [
                0 => 'Zero',
                1 => 'One',
                2 => 'Two',
                3 => 'Three',
                4 => 'Four',
                5 => 'Five',
                6 => 'Six',
                7 => 'Seven',
                8 => 'Eight',
                9 => 'Nine',
                10 => 'Ten',
                11 => 'Eleven',
                12 => 'Twelve',
                13 => 'Thirteen',
                14 => 'Fourteen',
                15 => 'Fifteen',
                16 => 'Sixteen',
                17 => 'Seventeen',
                18 => 'Eighteen',
                19 => 'Nineteen',
                20 => 'Twenty',
                30 => 'Thirty',
                40 => 'Forty',
                50 => 'Fifty',
                60 => 'Sixty',
                70 => 'Seventy',
                80 => 'Eighty',
                90 => 'Ninety',
            ];

            if ($number < 21) {
                return $words[$number];
            } elseif ($number < 100) {
                $tens = $words[10 * floor($number / 10)];
                $units = $number % 10;
                return $tens . ($units ? ' ' . $words[$units] : '');
            } elseif ($number < 1000) {
                $hundreds = $words[floor($number / 100)] . ' Hundred';
                $remainder = $number % 100;
                return $hundreds . ($remainder ? ' and ' . convertNumberToWords($remainder) : '');
            } elseif ($number < 100000) {
                $thousands = convertNumberToWords(floor($number / 1000)) . ' Thousand';
                $remainder = $number % 1000;
                return $thousands . ($remainder ? ' ' . convertNumberToWords($remainder) : '');
            } elseif ($number < 10000000) {
                $lakhs = convertNumberToWords(floor($number / 100000)) . ' Lakh';
                $remainder = $number % 100000;
                return $lakhs . ($remainder ? ' ' . convertNumberToWords($remainder) : '');
            } else {
                $crores = convertNumberToWords(floor($number / 10000000)) . ' Crore';
                $remainder = $number % 10000000;
                return $crores . ($remainder ? ' ' . convertNumberToWords($remainder) : '');
            }
        }
    }
    if (!function_exists('numberToIndianRupees')) {
        function numberToIndianRupees($number)
        {
            // Ensure $number is of type int or float
            if (!is_int($number) && !is_float($number)) {
                // You can handle the case where $number is not a valid type
                // Here, we are converting it to float, but you can customize as needed.
                $number = (float) $number;
            }

            $amount_in_words = convertNumberToWords(floor($number));
            $decimal_part = intval(($number - floor($number)) * 100);

            if ($decimal_part > 0) {
                $decimal_in_words = convertNumberToWords($decimal_part);
                return $amount_in_words . ' Rupees and ' . $decimal_in_words . ' Paisa';
            } else {
                return $amount_in_words . ' Rupees';
            }
        }
    }

        @endphp

            <div class="invoice-box">
                <div class="header_logo
        @if ($pdfData && isset($pdfData->invoice_alignment)) @if ($pdfData->invoice_alignment == 'center')
                center-align
            @elseif($pdfData->invoice_alignment == 'left')
                left-align
            @elseif($pdfData->invoice_alignment == 'right')
                right-align @endif
        @endif">
                    {{-- href="{{ Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5)) }}" --}}
                    {{-- @if (isset($pdfData['companyLogo']['invoiceTemporaryImageUrl'])) --}}
                    {{-- @if ($pdfData && isset($pdfData->invoice_logo_url))
                    <img src="{{ Storage::disk('s3')->temporaryUrl($pdfData->invoice_logo_url, now()->addHours(1)) }}"
                        alt="">
                    @endif --}}
                    {{-- <img src="image/Vector.png" alt="theparchi"> <br> --}}

                    {{-- <img src="{{asset('image/Vector.png')}}" alt="theparchi"> <br> --}}
                </div>
        {{-- @dd($goodsReceipt->buyerUser->bank_name); --}}
                <div>
                    <table>
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <h1>{{ $pdfData->receipt_note_heading ?? 'Receipt Note' }}</h1>
                                    #{{ strtoupper($goodsReceipt->goods_series) }}-{{ $goodsReceipt->series_num }}
                                </td>
                                <td></td>
                                <td style="width: 30%; text-align: right; padding-top:10px">
                                    <br> <br>
                                    <b>Date: {{ date('j-m-Y', strtotime($goodsReceipt->statuses[0]->created_at)) }}</b>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <table cellpadding="0" cellspacing="0" style="border-bottom:0px solid!;">
                    <tbody>


                        <tr class="information">
                            <td colspan="3" >
                                <table style="border-top: 1px solid;">
                                    <tr>
                                        <td style="border-right: 1px solid #000; text-align: left; width: 33%;">
                                            <b> SENDER:</b>
                                            {{ $goodsReceipt->senderUser->company_name ? ucfirst($goodsReceipt->senderUser->company_name) : ucfirst($goodsReceipt->senderUser->name) }}
                                            <br>
                                            @if (isset($goodsReceipt->senderUser->address)) <b>Address:</b> {{
                                            ucfirst($goodsReceipt->senderUser->address) }} <br> @endif

                                            @if (isset($goodsReceipt->senderUser->city)) <b>City:</b> {{ $goodsReceipt->senderUser->city }} <br> @endif
                                            @if (isset($goodsReceipt->senderUser->pincode)) <b>Pin Code:</b> {{ $goodsReceipt->senderUser->pincode }} <br> @endif
                                            @if (isset($goodsReceipt->senderUser->state)) <b>State:</b> {{ ucfirst($goodsReceipt->senderUser->state) }} <br> @endif
                                            @if (isset($goodsReceipt->senderUser->phone)) <b>Phone:</b> +91 {{ $goodsReceipt->senderUser->phone }} <br> @endif

                                            @if (isset($goodsReceipt->senderUser->gst_number))
                                            GSTIN: {{ strtoupper($goodsReceipt->senderUser->gst_number) }} <br>
                                            @endif

                                        </td>
                                        {{-- @dd($goodsReceipt); --}}
                                        {{-- NEW DB --}}
                                        <td style="text-align: left; width: 33%; {{ $pdfData->goodsReceipt_templete == 1 ? '' : 'border-right: 1px solid gray;' }}">
                                            @if (isset($goodsReceipt->buyerUser->receiver_name) && !empty($goodsReceipt->buyerUser->receiver_name))
                                                <b> RECEIVER </b> {{ ucfirst($goodsReceipt->buyerUser->receiver_name) }}<br>
                                            @elseif (isset($goodsReceipt->receiver_goods_receipts) && !empty($goodsReceipt->receiver_goods_receipts))
                                                <b> RECEIVER </b> {{ ucfirst($goodsReceipt->receiver_goods_receipts ) }}<br>
                                            @endif
                                            @if (!empty($goodsReceipt->buyerUser->details[0]->address))
                                                <b> Address: </b> {{ ucfirst($goodsReceipt->buyerUser->details[0]->address) }}<br>
                                            @endif
                                            @if (!empty($goodsReceipt->buyerUser->details[0]->city))
                                                <b>City:</b> {{ ucfirst($goodsReceipt->buyerUser->details[0]->city) }} <br>
                                            @endif
                                            @if (!empty($goodsReceipt->buyerUser->details[0]->pincode))
                                                <b>Pincode:</b> {{ $goodsReceipt->buyerUser->details[0]->pincode }} <br>
                                            @endif
                                            @if (!empty($goodsReceipt->buyerUser->details[0]->state))
                                                <b>State: </b> {{ ucfirst($goodsReceipt->buyerUser->details[0]->state) }} <br>
                                            @endif
                                            @if (!empty($goodsReceipt->buyerUser->details[0]->phone))
                                                <b>Phone: </b>+91 {{ $goodsReceipt->buyerUser->details[0]->phone }} <br>
                                            @endif
                                            @if (!empty($goodsReceipt->buyerUser->details[0]->email))
                                                <b>Email: </b>{{ $goodsReceipt->buyerUser->details[0]->email }}<br>
                                            @endif
                                        </td>

                                        {{-- @if ($pdfData->goodsReceipt_templete != 1)
                                        @if (isset($goodsReceipt->buyerUser))
                                        <td style="text-align: left; width: 33%;">
                                            <b> SHIP TO </b> <br>

                                            @if($goodsReceipt->user_detail_id)
                                            @if (isset($goodsReceipt->userDetails->address))  <b> Address: </b>{{ ucfirst($goodsReceipt->userDetails->address) }}<br> @endif
                                            @if (isset($goodsReceipt->userDetails->city))  <b>City:</b> {{ ucfirst($goodsReceipt->userDetails->city) }} <br> @endif
                                            @if (isset($goodsReceipt->userDetails->pincode))  <b>Pincode:</b> {{ ucfirst($goodsReceipt->userDetails->pincode) }} <br> @endif
                                            @if (isset($goodsReceipt->userDetails->state))  <b>State:</b> {{ ucfirst($goodsReceipt->userDetails->state) }} <br> @endif
                                            @if (!empty($goodsReceipt->buyerUser->phone))   <b>Phone: </b>+91 {{ $goodsReceipt->userDetails->phone }} <br> @endif

                                            @else
                                            @if (isset($goodsReceipt->buyerUser->address))  <b> Address: </b>{{ ucfirst($goodsReceipt->buyerUser->address) }}<br> @endif
                                            @if (isset($goodsReceipt->buyerUser->city))  <b>City:</b> {{ ucfirst($goodsReceipt->buyerUser->city) }} <br> @endif
                                            @if (isset($goodsReceipt->buyerUser->pincode))  <b>Pincode:</b> {{ ucfirst($goodsReceipt->buyerUser->pincode) }} <br> @endif
                                            @if (isset($goodsReceipt->buyerUser->state))  <b>State:</b> {{ ucfirst($goodsReceipt->buyerUser->state) }} <br> @endif
                                            @if (!empty($goodsReceipt->buyerUser->phone))   <b>Phone: </b>+91 {{ $goodsReceipt->buyerUser->phone }} <br> @endif
                                            @endif
                                            @if (isset($goodsReceipt->buyerUser->email))  <b>Email: </b>{{ $goodsReceipt->buyerUser->email }}<br> @endif

                                        </td>
                                        @endif
                                        @endif --}}
                                    </tr>
                                </table>

                            </td>
                        </tr>


                    </tbody>
                </table>
                <table style="text-align: left;">
                    <thead>
                        <tr class="heading">
                            <td>#</td>
                            @php
                                $columnNames = [];
                                foreach ($goodsReceipt->orderDetails as $detail) {
                                    foreach ($detail->columns as $column) {
                                        if (!in_array($column->column_name, $columnNames) && $column->column_value) {
                                            $columnNames[] = $column->column_name;
                                        }
                                    }
                                }
                                $showUnit = false;
                                $showRate = false;
                                $showQty = false;
                                $showAmount = false;
                                $showTax = false;
                                foreach ($goodsReceipt->orderDetails as $detail) {
                                    if ($detail->unit) {
                                        $showUnit = true;
                                    }
                                    if ($detail->rate) {
                                        $showRate = true;
                                    }
                                    if ($detail->qty) {
                                        $showQty = true;
                                    }
                                    if ($detail->tax) {
                                        $showTax = true;
                                    }
                                    if ($detail->total_amount) {
                                        $showAmount = true;
                                    }
                                }
                            @endphp
                            @foreach ($columnNames as $columnName)
                                <td style="width: auto">{{ strtoupper($columnName) }}</td>
                            @endforeach
                            @if ($showUnit)
                                <td>UNIT</td>
                            @endif
                            @if ($showRate)
                                <td>RATE</td>
                            @endif
                            @if ($showQty)
                                <td>QTY</td>
                            @endif
                            @if ($showTax)
                                <td>GST</td>
                            @endif
                            @if ($showAmount)
                            <td style="text-align: right">TOTAL</td>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php $total_rate = 0; @endphp
                        @foreach ($goodsReceipt->orderDetails as $index => $detail)
                            <tr class="item" style="border-bottom: 1px solid #000;">
                                @php $total_rate = $total_rate + floatval($detail->rate); @endphp
                                <td>{{ $index + 1 }}</td>
                                @foreach ($columnNames as $columnName)
                                    @php
                                        $columnValue = null;
                                        foreach ($detail->columns as $column) {
                                            if ($column->column_name == $columnName) {
                                                $columnValue = $column->column_value;
                                                break;
                                            }
                                        }
                                    @endphp
                                    <td style="width: auto">{{ strtoupper($columnValue) }}</td>
                                @endforeach
                                @if ($showUnit)
                                    <td>{{ $detail->unit }}</td>
                                @endif
                                @if ($showRate)
                                <td>
                                    @if (!is_null($detail->rate))
                                        ₹ {{ $detail->rate }}
                                    @endif
                                </td>
                                @endif
                                @if ($showQty)
                                    <td>{{ $detail->qty }}</td>
                                @endif
                                @if ($showTax)
                                    <td>{{ $detail->tax }}</td>
                                @endif
                                @if ($showAmount)
                                <td style="text-align: right">
                                    @if($detail->total_amount)
                                        &#8377; {{ $detail->total_amount }}
                                    @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach

                    </tbody>
                </table>

                <table style="width: 100%; border-bottom:1px solid!;  text-align: right; font-size: xx-small; line-height: normal;" >

                    @php
                    $taxTotals = []; // Array to store totals for each unique tax value
                    @endphp

                    @foreach($goodsReceipt->orderDetails as $index => $orderDetail)
                    @php
                    $tax = (float)$orderDetail['tax']; // Current tax value
                    $discount = (float)$orderDetail['discount']; // Current discount value
                    $totalWithoutTax = (float)$orderDetail['qty'] * (float)$orderDetail['rate']; // Total without tax for the current order detail

                    if (!$goodsReceipt->calculate_tax) {
                        $totalWithoutTax = $totalWithoutTax * 100 / (100 + $tax);
                        $taxAmount = 0; // No tax amount if calculateTax is false
                        $totalWithTax = $totalWithoutTax; // Total with tax is same as total without tax if calculateTax is false
                    } else {
                        $taxPercent = $tax / 100; // Tax percentage
                        $taxAmount = $totalWithoutTax * $taxPercent; // Tax amount for the current order detail
                        $totalWithTax = $totalWithoutTax + $taxAmount; // Total with tax for the current order detail
                    }

                    $discountWithoutTax = $orderDetail['discount'] / 100 * $totalWithoutTax;
                    $netSales = $totalWithoutTax - $discountWithoutTax; // Net sales after discount
                @endphp

                        @if (array_key_exists($tax, $taxTotals))
                            {{-- If tax already exists in taxTotals, accumulate quantities and totals --}}
                            @php
                                $taxTotals[$tax]['qty'] += $orderDetail['qty'];
                                $taxTotals[$tax]['totalWithoutTax'] += $totalWithoutTax;
                                $taxTotals[$tax]['totalWithTax'] += $totalWithTax;
                                $taxTotals[$tax]['netSales'] += $netSales;
                                $taxTotals[$tax]['discount'] += $orderDetail['discount']; // Accumulate discount
                            @endphp
                        @else
                            {{-- If tax is encountered for the first time, initialize values in taxTotals --}}
                            @php
                                $taxTotals[$tax] = [
                                    'qty' => $orderDetail['qty'],
                                    'totalWithoutTax' => $totalWithoutTax,
                                    'totalWithTax' => $totalWithTax,
                                    'netSales' => $netSales, // Initialize net sales
                                    'discount' => $orderDetail['discount'], // Initialize discount
                                ];
                            @endphp
                        @endif
                    @endforeach

                    @if($tax)
                    {{-- Output accumulated totals for each unique tax value --}}
                    {{-- @dump($totals['totalWithoutTax']- ($totals['totalWithoutTax'] * $taxPercent) ) --}}
                        @foreach ($taxTotals as $tax => $totals)
                        @php
                        $taxPercent = $tax / 100; // Calculate tax percentage for the current tax total
                        @endphp
                                    <tr>
                                        <td style="text-align: right; margin-top:5px;">
                                            {{-- @dd($goodsReceipt->calculate_tax,$totals, array_key_exists('totalWithoutTax', $totals) ? $totals['totalWithoutTax'] : 'N/A' ); --}}
                                            @if (isset($goodsReceipt->calculate_tax  ) && !empty($goodsReceipt->calculate_tax) && $goodsReceipt->calculate_tax == 1 )
                                            <td>
                                                <td>Sales at {{ $tax }}% : {{ array_key_exists('totalWithoutTax', $totals) ? $totals['totalWithoutTax'] : 'N/A' }}</td>
                                            </td>
                                        </td>
                                    </tr>
                                    @if($discount)
                                    @php
                                    $discountWithoutTax = $discount / 100 * (array_key_exists('totalWithoutTax', $totals) ? $totals['totalWithoutTax'] : 0);
                                    $netSales = (array_key_exists('totalWithoutTax', $totals) ? $totals['totalWithoutTax'] : 0) - $discountWithoutTax;
                                    @endphp

                                        @if(!empty($discount) && !empty($discountWithoutTax))
                                            <tr>
                                                <td>
                                                    <td>
                                                        <td style="text-align: right;">Discount at {{$discount }}% :  {{ $discountWithoutTax  }}  </td>
                                                    </td>
                                                </td>
                                            </tr>
                                        @endif

                                        <tr><td><td><td style="text-align: right;"> Net Sale {{ $tax }}%:  {{ $netSales }} </td></td></td></tr>
                                        @php
                                        $taxableAmount = $netSales;
                                        @endphp
                                    @else
                                    @php
                                    $taxableAmount = array_key_exists('totalWithoutTax', $totals) ? $totals['totalWithoutTax'] : 0;
                                    @endphp
                                    @endif
                                        @if(isset($goodsReceipt->buyerUser->state) && isset(Auth::user()->state) && strtoupper($goodsReceipt->buyerUser->state) === strtoupper(Auth::user()->state))
                                            <tr>
                                                <td><td><td>CGST at {{ ((float)$tax )/2}}% : {{ number_format(($taxableAmount * $taxPercent) / 2, 2) }}</td></td></td>
                                            </tr>
                                            <tr>
                                                <td><td><td>SGST at {{ ((float)$tax )/2}}% : {{ number_format(($taxableAmount * $taxPercent) / 2, 2) }}</td></td></td>
                                            </tr>
                                        @elseif(isset($goodsReceipt->buyerUser->state) == false )
                                            <tr>
                                                <td><td> <td>CGST at {{ ((float)$tax )/2}}% : {{ number_format(($taxableAmount * $taxPercent) / 2, 2) }}</td></td></td>
                                            </tr>
                                            <tr>
                                                <td><td><td>SGST at {{ ((float)$tax )/2}}% : {{ number_format(($taxableAmount * $taxPercent) / 2, 2) }}</td></td></td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td><td><td>IGST at {{ $tax }}% : {{ number_format($taxableAmount * $taxPercent, 2) }}</td></td></td>
                                            </tr>
                                        @endif
                                    @else
                                    @php
                                    $withoutTax = number_format(array_key_exists('totalWithoutTax', $totals) ? $totals['totalWithoutTax'] : 0, 2);
                                    $discountAmount = number_format($discount / 100 * $withoutTax, 2);
                                    $netSales = number_format($withoutTax - $discountAmount, 2);
                                    @endphp
                                        <tr style="margin-top:5px;" >
                                            <td><td><td>Sales at {{ $tax }}% : {{ number_format( $totals['totalWithoutTax'], 2)}}</td></td></td>
                                        </tr>
                                        @if($discount)
                                        <tr  > <td><td><td style="text-align: right;"> Discount at {{ $discount }}% :  {{ $discountAmount  }}  </td></td></td> </tr>
                                        <tr > <td><td><td style="text-align: right;"> Net Sale {{ $tax }}%: {{ $netSales }}  </td></td></td></tr>
                                        @endif
                                        @if(isset($goodsReceipt->buyerUser->state) && isset(Auth::user()->state) && strtoupper($goodsReceipt->buyerUser->state) === strtoupper(Auth::user()->state))
                                        <tr>
                                            <td><td><td>CGST at {{ ((float)$tax )/2}}% : {{ number_format(($netSales * $taxPercent) / 2, 2) }}</td></td></td>
                                        </tr>
                                        <tr>
                                            <td><td><td>SGST at {{ ((float)$tax )/2}}% : {{ number_format(($netSales * $taxPercent) / 2, 2) }}</td></td></td>
                                        </tr>
                                        @elseif(isset($goodsReceipt->buyerUser->state) == false )
                                        <tr>
                                        <td><td> <td>CGST at {{ ((float)$tax )/2}}% : {{ number_format(($netSales * $taxPercent) / 2, 2) }}</td></td></td>
                                        </tr>
                                        <tr>
                                            <td><td><td>SGST at {{ ((float)$tax )/2}}% : {{ number_format(($netSales * $taxPercent) / 2, 2) }}</td></td></td>
                                        </tr>
                                        @else
                                            <tr>
                                            <td><td> <td>IGST at {{ $tax }}% : {{ number_format($netSales * $taxPercent, 2) }}</td></td></td>
                                            </tr>
                                        @endif

                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    {{-- @dd($goodsReceipt)      --}}
                </table>
                <table style="width: 100%;  margin-top: 5px; " >
                    <thead>
                        @if($goodsReceipt->comment)
                            <tr>
                                <td colspan="2">
                                    <b>Comment:</b>
                                    <p>{{ $goodsReceipt->comment }}</p>
                                </td>

                            </tr>
                        @endif
                        <tr>
                            <!-- Left side for comments and details -->
                            <td style="width: 50%;" class=" @if($goodsReceipt->comment) total @endif">
                                @if($goodsReceipt->total)
                                    <b><small>AMOUNT IN WORDS</small></b>
                                    <br>
                                    {{ numberToIndianRupees($goodsReceipt->total) }}
                                @endif
                                <!-- Additional details or comments can go here -->
                            </td>

                            <!-- Right side for total quantity and grand total -->
                            <td style="width: 50%; text-align: right;">
                                @if($goodsReceipt->total_qty)
                                    <b>Total Qty:</b> {{ $goodsReceipt->total_qty }}
                                    <br>
                                @endif
                                @if ($goodsReceipt->round_off != 0)
                                <b>Round Off</b>
                                    <p>&#8377; {{$goodsReceipt->round_off}}</p>
                                @endif
                                <br>
                                @if($goodsReceipt->total)
                                    <b>Grand Total</b>
                                    &#8377; {{ $goodsReceipt->total }}
                                    <br>
                                @endif
                                {{-- @if ($pdfData && isset($pdfData->signature_seller))
                                    <img style="height: 70px" src="{{ Storage::disk('s3')->temporaryUrl($pdfData->signature_seller, now()->addHours(1)) }}" alt="">
                                    <br>
                                    <p style="font-size: 10px">Authorized Signature</p>
                                @endif --}}
                                @if ($pdfData && $pdfData->signature_option_receipt_note === 'Signature' && isset($pdfData->signature_receipt_note))
                                <img style="height: 70px" src="{{ Storage::disk('s3')->temporaryUrl($pdfData->signature_receipt_note, now()->addHours(1)) }}" alt="">
                                <br>
                                <div><small style="font-size: 8px; font-weight: normal;">Authorized Signature</small></div>
                                @endif
                            </td>
                        </tr>
                    </thead>

                    <tbody>
                        <tr class=" @if ($termsAndConditions && count($termsAndConditions) > 0) total @endif">
                            <td colspan="3">
                                <table>
                                    <tbody>
                                        {{-- <tr>
                                            <td style="font-size: smaller;">
                                                @if (isset($goodsReceipt->buyerUser->bank_name))
                                                <b>BANK DETAILS</b>
                                                <br>
                                                <b>BANK NAME : </b> {{$goodsReceipt->buyerUser->bank_name}}
                                                <br>
                                                <b>BRANCH : </b> {{$goodsReceipt->buyerUser->branch_name}}
                                                <br>
                                                <b>ACCOUNT NO :</b> {{$goodsReceipt->buyerUser->bank_account_no}}
                                                <br>
                                                <b>IFSC CODE :</b> {{$goodsReceipt->buyerUser->ifsc_code}}
                                                @endif
                                            </td>
                                        </tr> --}}
                                        <tr>
                                            <div>
                                                @if ($termsAndConditions && count($termsAndConditions) > 0)
                                                <div style="font-size: smaller;">
                                                    <b>TERMS AND CONDITIONS</b> <br>
                                                    @foreach ($termsAndConditions as $condition)
                                                    <small>*{{ $condition->content }}</small> <br>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="footer">
                <!-- Footer content goes here -->
                <div style="font-size: 10px;">
                    {{-- @if (!$pdfData || !isset($pdfData->signature_seller)) --}}
                    * This is a computer-generated {{ $pdfData->receipt_note_heading ?? 'Receipt Note' }} and does not require a physical signature
                    {{-- @endif --}}
                </div> <br>
                {{-- @if(isset($pdfData->invoice_stamp) && $pdfData->invoice_stamp == 1) --}}
                <img src="https://theparchi.com/image/Vector.png" alt="theparchi"> <br>

                <small>POWERED BY</small> <a href="www.theparchi.com" style="color: black;">www.TheParchi.com</a>
                {{-- @endif --}}
            </div>

    </body>

    </html>
