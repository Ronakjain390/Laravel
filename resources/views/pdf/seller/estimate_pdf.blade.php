<!DOCTYPE html>
<html>

<head>
    <title> Quotations</title>
    {{-- @dd($estimate); --}}
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
    {{-- @dd($estimate->buyerUser->bank_name); --}}
    {{-- @dump($estimate) --}}
            <div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                      <td colspan="2" style="text-align: left;">
                        {{-- <h1 style="margin: 0;">{{ 'Quotations' }}</h1> --}}
                        <h1 style="margin: 0;">{{ $pdfData->estimate_heading ?? 'Quotations' }}</h1>
                      </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align: left;">
                          @if(isset($estimate->purchase_order_series) && !empty($estimate->purchase_order_series))
                            {{-- <b>PO No. {{ $estimate->purchase_order_series }}</b> --}}
                          @endif
                        </td>
                        <td style="width: 50%; text-align: right;">
                          @if(isset($estimate->eway_bill_no) && !empty($estimate->eway_bill_no))
                            <b>E-way Bill No: {{ $estimate->eway_bill_no }}</b>
                          @endif
                        </td>
                      </tr>
                    <tr>
                      <td style="width: 50%; text-align: left;">
                        <b>Quotations No. : {{ strtoupper($estimate->estimate_series) }}-{{ $estimate->series_num }}</b>
                      </td>
                      <td style="width: 50%; text-align: right;">
                        @if(isset($estimate->statuses) && count($estimate->statuses) > 0)
                          <b>Date: {{ date('d-m-Y', strtotime($estimate->estimate_date)) }}</b>
                        @else
                          <b>Date: {{ date('d-m-Y') }}</b>
                        @endif
                      </td>
                    </tr>

                  </table>
            </div>

            <table cellpadding="0" cellspacing="0" style="border-bottom:0px solid!;">
                <tbody>
                    <tr class="information">
                        <td colspan="3">
                            <table style="border-top: 1px solid;">
                                <tr>
                                    <td style="border-right: 1px solid #000; text-align: left;">

                                        <b>From:</b> : {{ ucfirst($estimate->sellerUser->company_name ?? $estimate->sellerUser->name) }}
                                        <br>
                                        @if (isset($estimate->sellerUser->address)) <b>Address:</b> {{
                                        ucfirst($estimate->sellerUser->address) }} <br> @endif
                                        @if (isset($estimate->sellerUser->city)) <b>City:</b> {{
                                        $estimate->sellerUser->city }} <br> @endif
                                        @if (isset($estimate->sellerUser->pincode)) <b>Pin Code:</b> {{
                                        $estimate->sellerUser->pincode }} <br> @endif
                                        @if (isset($estimate->sellerUser->state)) <b>State:</b> {{
                                        ucfirst($estimate->sellerUser->state) }} <br> @endif
                                        @if (isset($estimate->sellerUser->phone)) <b>Phone:</b> +91 {{
                                        $estimate->sellerUser->phone }} <br> @endif
                                        @if (isset($estimate->sellerUser->pancard))
                                        <b>PAN:</b> {{ strtoupper($estimate->sellerUser->pancard) }} <br>
                                        @endif
                                        @if (isset($estimate->sellerUser->gst_number))
                                        <b>GSTIN:</b> {{ strtoupper($estimate->sellerUser->gst_number) }} <br>
                                        @endif
                                    </td>
                                    <td style="border-right: 1px solid gray; text-align: left;">


                                        @if (isset($estimate->buyerUser->name))
                                        <b> Bill To </b> {{ ucfirst($estimate->buyerUser->name) }}<br>
                                        @elseif (isset($estimate->buyer))
                                        <b> Bill To </b> {{ ucfirst($estimate->buyer ) }}<br>
                                        @else
                                        <b> Bill To </b> Default<br>
                                        @endif
                                        @if (isset($estimate->buyerUser->address)) <b> Address: </b> {{
                                        ucfirst($estimate->buyerUser->address) }}<br> @endif
                                        @if (isset($estimate->buyerUser->city)) <b>City:</b> {{
                                        ucfirst($estimate->buyerUser->city) }} <br> @endif
                                        @if (isset($estimate->buyerUser->pincode)) <b>Pincode:</b> {{
                                        ucfirst($estimate->buyerUser->pincode) }} <br> @endif
                                        @if (isset($estimate->buyerUser->state)) <b>State: </b> {{
                                        ucfirst($estimate->buyerUser->state) }} <br> @endif
                                        @if (isset($estimate->buyerUser->phone)) <b>Phone: </b>+91 {{
                                        $estimate->buyerUser->phone }} <br> @endif
                                        @if (isset($estimate->buyerUser->email)) <b>Email: </b>{{
                                        $estimate->buyerUser->email }}<br>
                                        @endif
                                        @if (isset($estimate->buyerUser->gst_number))
                                        <b>GSTIN:</b> {{ strtoupper($estimate->buyerUser->gst_number) }} <br>
                                        @endif
                                    </td>
                                    @if (isset($estimate->buyerUser->address))
                                    <td style="text-align: left;">
                                        <b> SHIP TO </b> <br>
                                        @if (isset($estimate->buyerUser->address)) <b> Address: </b>{{
                                        ucfirst($estimate->buyerUser->address) }}<br> @endif
                                        @if (isset($estimate->buyerUser->city)) <b>City:</b> {{
                                        ucfirst($estimate->buyerUser->city) }} <br> @endif
                                        @if (isset($estimate->buyerUser->pincode)) <b>Pincode:</b> {{
                                        ucfirst($estimate->buyerUser->pincode) }} <br> @endif
                                        @if (isset($estimate->buyerUser->state)) <b>State:</b> {{
                                        ucfirst($estimate->buyerUser->state) }} <br> @endif
                                        @if (isset($estimate->buyerUser->phone)) <b>Phone: </b>+91 {{
                                        $estimate->buyerUser->phone }} <br> @endif
                                        @if (isset($estimate->buyerUser->email)) <b>Email: </b>{{
                                        $estimate->buyerUser->email }}<br> @endif
                                        @if (isset($estimate->buyerUser->gst_number))
                                        <b>GSTIN:</b> {{ strtoupper($estimate->buyerUser->gst_number) }} <br>
                                        @endif
                                    </td>
                                    @endif
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
                            foreach ($estimate->orderDetails as $detail) {
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
                            foreach ($estimate->orderDetails as $detail) {
                                if ($detail->unit) {
                                    $showUnit = true;
                                }
                                if ($detail->rate) {
                                    $showRate = true;
                                }
                                if ($detail->qty) {
                                    $showQty = true;
                                }
                                if (!empty($detail->tax) && $detail->tax != '0' && $detail->tax != '0.00') {
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
                    @foreach ($estimate->orderDetails as $index => $detail)
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
                            @if ($showTax && !empty($detail->tax) && $detail->tax != '0' && $detail->tax != '0.00')
                            <td>{{ is_numeric($detail->tax) && intval($detail->tax) == floatval($detail->tax) ? intval($detail->tax) : $detail->tax }}%</td>
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

            <table style="width: 100%; text-align: right; font-size: xx-small; line-height: normal;">
                @php
                $taxTotals = []; // Array to store totals for each unique tax value
                $totalWithoutTax = 0;
                $totalDiscount = 0;
                $hasOnlyDiscount = false;
                @endphp

                @foreach($estimate->orderDetails as $index => $orderDetail)
                @php
                $tax = (float)$orderDetail['tax']; // Current tax value
                $discount = (float)$orderDetail['discount']; // Current discount value
                $itemTotal = (float)$orderDetail['qty'] * (float)$orderDetail['rate']; // Total for the current order detail
                $totalWithoutTax += $itemTotal;
                $totalDiscount += ($discount / 100) * $itemTotal;

                if ($discount > 0 && $tax == 0) {
                    $hasOnlyDiscount = true;
                }

                if (!$estimate->calculate_tax) {
                    $taxAmount = 0;
                } else {
                    $taxPercent = $tax / 100;
                    $taxAmount = $itemTotal * $taxPercent;
                }

                $netSales = $itemTotal - (($discount / 100) * $itemTotal);

                if (!isset($taxTotals[$tax])) {
                    $taxTotals[$tax] = [
                        'qty' => 0,
                        'totalWithoutTax' => 0,
                        'totalWithTax' => 0,
                        'netSales' => 0,
                        'discount' => 0,
                        'discountPercent' => $discount,
                    ];
                }

                $taxTotals[$tax]['qty'] += $orderDetail['qty'];
                $taxTotals[$tax]['totalWithoutTax'] += $itemTotal;
                $taxTotals[$tax]['totalWithTax'] += $itemTotal + $taxAmount;
                $taxTotals[$tax]['netSales'] += $netSales;
                $taxTotals[$tax]['discount'] += ($discount / 100) * $itemTotal;
                @endphp
                @endforeach

                @foreach ($taxTotals as $tax => $totals)
                @php
                $taxPercent = $tax / 100;
                $taxableAmount = $totals['netSales'];
                $discountPercent = $totals['discountPercent'];
                @endphp

                <tr>
                    <td></td>
                    <td></td>
                    <td>Sales{{ $tax > 0 ? " at {$tax}%" : "" }}: {{ number_format($totals['totalWithoutTax'], 2) }}</td>
                </tr>

                @if($totals['discount'] > 0)
                <tr>
                    <td></td>
                    <td></td>
                    <td style="text-align: right;">Discount at {{ $discountPercent }}%: {{ number_format($totals['discount'], 2) }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td style="text-align: right;">Net Sale at {{ $tax > 0 ? " at {$tax}%" : "" }}: {{ number_format($totals['netSales'], 2) }}</td>
                </tr>
                @endif

                @if($tax > 0)
                    @if(isset($estimate->buyerUser->state) && isset(Auth::user()->state) && strtoupper($estimate->buyerUser->state) === strtoupper(Auth::user()->state))
                    <tr>
                        <td></td>
                        <td></td>
                        <td>CGST at {{ $tax/2 }}%: {{ number_format(($taxableAmount * $taxPercent) / 2, 2) }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>SGST at {{ $tax/2 }}%: {{ number_format(($taxableAmount * $taxPercent) / 2, 2) }}</td>
                    </tr>
                    @else
                    <tr>
                        <td></td>
                        <td></td>
                        <td>IGST at {{ $tax }}%: {{ number_format($taxableAmount * $taxPercent, 2) }}</td>
                    </tr>
                    @endif
                @endif
                @endforeach

                {{-- @if($hasOnlyDiscount)
                <tr>
                    <td></td>
                    <td></td>
                    <td>Total Discount: {{ number_format($totalDiscount, 2) }}</td>
                </tr>
                @endif --}}
            </table>

            <table style="width: 100%; margin-top: 5px;">
                <thead>
                    @if (!empty($estimate->comment))
                    <tr>
                        <td colspan="2">
                            <b>Comment:</b>
                            <p>{{ $estimate->comment }}</p>
                        </td>
                    </tr>
                    @endif

                    <tr class="total">
                        <td colspan="2">
                            @if ($showAmount)
                            <b><small>AMOUNT IN WORDS</small></b>
                            <br>
                            {{ numberToIndianRupees($estimate->total) }}
                            @endif
                        </td>
                        <td style="width: 30%; text-align: right;">
                            @if ($estimate->round_off != 0)
                            <b>Round Off</b>
                                <p>&#8377; {{$estimate->round_off}}</p>
                            @endif
                            <br>
                            @if ($showAmount)
                            <b> Grand Total </b>
                            &#8377; {{ $estimate->total }}
                            @endif
                            <br>
                            @if ($pdfData && $pdfData->signature_option_seller === 'Signature' && isset($pdfData->signature_seller))
                            <img style="height: 70px" src="{{ Storage::disk('s3')->temporaryUrl($pdfData->signature_seller, now()->addHours(1)) }}" alt="">
                            <br>
                            <div><small style="font-size: 8px; font-weight: normal;">Authorized Signature</small></div>
                            @endif
                        </td>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td colspan="3">
                            <table>
                                <tbody>
                                    <tr>
                                        <td style="font-size: smaller;">
                                            @if (isset($estimate->sellerUser->bank_name))
                                            <b>BANK DETAILS</b>
                                            <br>
                                            <b>BANK NAME : </b> {{$estimate->sellerUser->bank_name}}
                                            <br>
                                            <b>BRANCH : </b> {{$estimate->sellerUser->branch_name}}
                                            <br>
                                            <b>ACCOUNT NO :</b> {{$estimate->sellerUser->bank_account_no}}
                                            <br>
                                            <b>IFSC CODE :</b> {{$estimate->sellerUser->ifsc_code}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <hr style="border-top: 1px solid #000; margin: 10px 0;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            @if ($termsAndConditions && count($termsAndConditions) > 0)
                                            <div style="font-size: smaller;">
                                                <b>TERMS AND CONDITIONS</b> <br>
                                                @foreach ($termsAndConditions as $condition)
                                                <small>*{{ $condition->content }}</small> <br>
                                                @endforeach
                                            </div>
                                            @endif
                                        </td>
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
            {{-- <div style="font-size: 10px;">
                @if (!$pdfData || !isset($pdfData->signature_seller))
                * This is a computer-generated {{ $pdfData->invoice_heading ?? 'Estimate' }} and does not require a physical signature
                @endif
            </div>  --}}
            <div style="font-size: 10px;">
                @if ($pdfData && $pdfData->signature_option_seller === 'FooterStamp')
                    * This is a computer-generated {{ $pdfData->estimate_heading ?? 'Quotations' }} and does not require a physical signature
                @endif
            </div>
            <br>
            @if(isset($pdfData->invoice_stamp) && $pdfData->invoice_stamp == 1)
            <img src="https://theparchi.com/image/Vector.png" alt="theparchi"> <br>

            <small>POWERED BY</small> <a href="www.theparchi.com" style="color: black;">www.TheParchi.com</a>
            @endif
        </div>
</body>

</html>
