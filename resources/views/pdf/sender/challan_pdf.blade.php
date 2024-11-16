<!DOCTYPE html>
<html>

<head>
    <title> Challan</title>
    <style>
        .challan-box {
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

        .challan-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .challan-box table td {
            /* padding: 5px; */
            vertical-align: top;
        }

        .challan-box table tr td:nth-child(2),
        .text-right {
            /* text-align: right; */
        }

        .challan-box table tr.top table td {
            padding-bottom: 20px;
        }

        .challan-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .challan-box table tr.information table td {
            padding-bottom: 20px;
            font-size: smaller;
            line-height: 12px;

        }

        .challan-box table tr.heading td {
            background: black;
            color: white;
            margin: 0;
            font-size: 10px;
        }

        .challan-box table tr.details td {
            padding-bottom: 20px;
        }

        .challan-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
            /* Add this line to remove spacing between cells */
        }

        .challan-box table td {
            /* padding: 5px; */
            vertical-align: top;
        }


        .challan-box table tr.item.last td {
            border-bottom: none;
        }

        .challan-box table tr.total td:nth-child(2) {
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
            margin-top: 15px;
        }


        .footer img {
            height: 35px;
            margin: 8px;
            margin-bottom: 10px;
        }

        .header_logo img {
            height: 100px;
            /* width: 700px; */
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
            .challan-box table tr.top table td {
                width: 100%;
                display: block;
                /* text-align: center; */
            }

            .challan-box table tr.information table td {
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
        {{-- @dd($challan); --}}
        <div class="challan-box">
            <div class="header_logo
                @if ($pdfData && isset($pdfData->challan_alignment)) @if ($pdfData->challan_alignment == 'center')
                        center-align
                    @elseif($pdfData->challan_alignment == 'left')
                        left-align
                    @elseif($pdfData->challan_alignment == 'right')
                        right-align @endif
                @endif">

                @if ($pdfData && isset($pdfData->challan_logo_url))
                <img src="{{ Storage::disk('s3')->temporaryUrl($pdfData->challan_logo_url, now()->addHours(1)) }}"
                    alt="">
                @endif
            </div>
            <div>
                <table>
                    <tbody>
                        <tr>
                            <td colspan="2">
                                    <h1>{{ empty($pdfData->challan_heading) ? "Challan" : $pdfData->challan_heading }}</h1>
                                    @if(!is_null($challan->series_num))
                                #{{ strtoupper($challan->challan_series) }}-{{ $challan->series_num }}
                                @endif

                            </td>
                            <td></td>
                            <td style="width: 30%; text-align: right; padding-top:10px">
                                <br> <br>
                                    @if(isset($challan->statuses[0]))
                                        <b>Date: {{ date('j-m-Y', strtotime($challan->challan_date)) }}</b>
                                    @else
                                        <b>Date: N/A</b>
                                    @endif
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
                                        {{ $challan->senderUser->company_name ? ucfirst($challan->senderUser->company_name) : ucfirst($challan->senderUser->name) }}
                                        <br>
                                        @if (isset($challan->senderUser->address)) <b>Address:</b> {{
                                        ucfirst($challan->senderUser->address) }} <br> @endif

                                        @if (isset($challan->senderUser->city)) <b>City:</b> {{ $challan->senderUser->city }} <br> @endif
                                        @if (isset($challan->senderUser->pincode)) <b>Pin Code:</b> {{ $challan->senderUser->pincode }} <br> @endif
                                        @if (isset($challan->senderUser->state)) <b>State:</b> {{ ucfirst($challan->senderUser->state) }} <br> @endif
                                        @if (isset($challan->senderUser->phone)) <b>Phone:</b> +91 {{ $challan->senderUser->phone }} <br> @endif

                                        @if (isset($challan->senderUser->gst_number))
                                        <b>GSTIN:</b> {{ strtoupper($challan->senderUser->gst_number) }} <br>
                                        @endif

                                    </td>
                                    {{-- @dd($challan); --}}
                                    {{-- NEW DB --}}
                                    <td style="text-align: left; width: 33%; {{ $pdfData->challan_templete == 1 ? '' : 'border-right: 1px solid gray;' }}">
                                        @if(isset($challan->receiver))
                                            <b> RECEIVER </b> {{ ucfirst($challan->receiver) }}<br>
                                        @else
                                            <b> RECEIVER </b> Default<br>
                                        @endif

                                        @if (isset($challan->receiverUser->address) && !empty($challan->receiverUser->address))
                                            <b>Address: </b> {{ ucfirst($challan->receiverUser->address) }}<br>
                                        @endif

                                        @if (isset($challan->receiverUser->city) && !empty($challan->receiverUser->city))
                                            <b>City:</b> {{ ucfirst($challan->receiverUser->city) }} <br>
                                        @endif

                                        @if (isset($challan->receiverUser->pincode) && !empty($challan->receiverUser->pincode))
                                            <b>Pincode:</b> {{ ucfirst($challan->receiverUser->pincode) }} <br>
                                        @endif

                                        @if (isset($challan->receiverUser->state) && !empty($challan->receiverUser->state))
                                            <b>State: </b> {{ ucfirst($challan->receiverUser->state) }} <br>
                                        @endif

                                        @if (isset($challan->receiverUser->phone) && !empty($challan->receiverUser->phone))
                                            <b>Phone: </b>+91 {{ $challan->receiverUser->phone }} <br>
                                        @endif

                                        @if (isset($challan->receiverUser->email) && !empty($challan->receiverUser->email))
                                            <b>Email: </b>{{ $challan->receiverUser->email }}<br>
                                        @endif
                                    </td>
                                    {{-- OLD DB --}}
                                    {{-- <td style="border-right: 1px solid gray; text-align: left;">
                                        @if (isset($challan->receiverUser->receiver_name)) <b> RECEIVER </b> {{ ucfirst($challan->receiverUser->receiver_name) }}<br> @endif
                                        @if (isset($challan->receiverDetails->address)) <b> Address: </b> {{ ucfirst($challan->receiverDetails->address) }}<br> @endif
                                        @if (isset($challan->receiverDetails->city)) <b>City:</b> {{ ucfirst($challan->receiverDetails->city) }} <br> @endif
                                        @if (isset($challan->receiverDetails->pincode)) <b>Pincode:</b> {{ ucfirst($challan->receiverDetails->pincode) }} <br> @endif
                                        @if (isset($challan->receiverDetails->state)) <b>State: </b> {{ ucfirst($challan->receiverDetails->state) }} <br> @endif
                                        @if (isset($challan->receiverDetails->phone)) <b>Phone: </b>+91 {{ $challan->receiverDetails->phone }} <br> @endif
                                        @if (isset($challan->receiverDetails->email)) <b>Email: </b>{{ $challan->receiverDetails->email }}<br> @endif
                                    </td> --}}
                                    {{-- OLD DB --}}
                                    @if ($pdfData->challan_templete != 1)
                                    @if (isset($challan->receiverUser))
                                    <td style="text-align: left; width: 33%;">
                                        <b> SHIP TO </b> <br>
                                        {{-- OLD DB --}}
                                        {{-- @if (isset($challan->receiverDetails->address)) <b> Address: </b> {{ ucfirst($challan->receiverDetails->address) }}<br> @endif
                                        @if (isset($challan->receiverDetails->city)) <b>City:</b> {{ ucfirst($challan->receiverDetails->city) }} <br> @endif
                                        @if (isset($challan->receiverDetails->pincode)) <b>Pincode:</b> {{ ucfirst($challan->receiverDetails->pincode) }} <br> @endif
                                        @if (isset($challan->receiverDetails->state)) <b>State: </b> {{ ucfirst($challan->receiverDetails->state) }} <br> @endif
                                        @if (isset($challan->receiverDetails->phone)) <b>Phone: </b>+91 {{ $challan->receiverDetails->phone }} <br> @endif
                                        @if (isset($challan->receiverDetails->email)) <b>Email: </b>{{ $challan->receiverDetails->email }}<br> @endif --}}
                                        {{-- OLD DB --}}
                                        {{-- NEW DB --}}
                                        @if($challan->user_detail_id)
                                        @if (isset($challan->userDetails->address))  <b> Address: </b>{{ ucfirst($challan->userDetails->address) }}<br> @endif
                                        @if (isset($challan->userDetails->city))  <b>City:</b> {{ ucfirst($challan->userDetails->city) }} <br> @endif
                                        @if (isset($challan->userDetails->pincode))  <b>Pincode:</b> {{ ucfirst($challan->userDetails->pincode) }} <br> @endif
                                        @if (isset($challan->userDetails->state))  <b>State:</b> {{ ucfirst($challan->userDetails->state) }} <br> @endif
                                        @if (!empty($challan->receiverUser->phone))   <b>Phone: </b>+91 {{ $challan->userDetails->phone }} <br> @endif

                                        @else
                                        @if (isset($challan->receiverUser->address))  <b> Address: </b>{{ ucfirst($challan->receiverUser->address) }}<br> @endif
                                        @if (isset($challan->receiverUser->city))  <b>City:</b> {{ ucfirst($challan->receiverUser->city) }} <br> @endif
                                        @if (isset($challan->receiverUser->pincode))  <b>Pincode:</b> {{ ucfirst($challan->receiverUser->pincode) }} <br> @endif
                                        @if (isset($challan->receiverUser->state))  <b>State:</b> {{ ucfirst($challan->receiverUser->state) }} <br> @endif
                                        @if (!empty($challan->receiverUser->phone))   <b>Phone: </b>+91 {{ $challan->receiverUser->phone }} <br> @endif
                                        @endif
                                        @if (isset($challan->receiverUser->email))  <b>Email: </b>{{ $challan->receiverUser->email }}<br> @endif
                                        {{-- NEW DB --}}
                                    </td>
                                    @endif
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
                            foreach ($challan->orderDetails as $detail) {
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
                            foreach ($challan->orderDetails as $detail) {
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
                            <td>TAX</td>
                        @endif
                        @if ($showAmount)
                        <td style="text-align: right">TOTAL</td>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $total_rate = 0; @endphp
                    @foreach ($challan->orderDetails as $index => $detail)
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
                $totalWithoutTax = 0;
                $totalDiscount = 0;
                @endphp

                @foreach($challan->orderDetails as $index => $orderDetail)
                @php
                $tax = (float)$orderDetail['tax']; // Current tax value
                $discount = (float)$orderDetail['discount']; // Current discount value
                $itemTotal = (float)$orderDetail['qty'] * (float)$orderDetail['rate']; // Total for the current order detail
                $totalWithoutTax += $itemTotal;
                $totalDiscount += ($discount / 100) * $itemTotal;

                if ($tax > 0) {
                    if (!isset($taxTotals[$tax])) {
                        $taxTotals[$tax] = [
                            'totalWithoutTax' => 0,
                            'discount' => 0,
                        ];
                    }
                    $taxTotals[$tax]['totalWithoutTax'] += $itemTotal;
                    $taxTotals[$tax]['discount'] += ($discount / 100) * $itemTotal;
                }
                @endphp
                @endforeach

                @if(count($taxTotals) > 0)
                    @foreach ($taxTotals as $tax => $totals)
                    @php
                    $taxPercent = $tax / 100;
                    $discountAmount = $totals['discount'];
                    $netSales = $totals['totalWithoutTax'] - $discountAmount;
                    @endphp
                    <tr>
                        <td><td><td>Sales at {{ $tax }}% : {{ number_format($totals['totalWithoutTax'], 2) }}</td></td></td>
                    </tr>
                    @if($discountAmount > 0)
                    <tr>
                        <td><td><td>Discount : {{ number_format($discountAmount, 2) }}</td></td></td>
                    </tr>
                    <tr>
                        <td><td><td>Net Sales at {{ $tax }}% : {{ number_format($netSales, 2) }}</td></td></td>
                    </tr>
                    @endif
                    @if(isset($challan->buyerUser->state) && isset(Auth::user()->state) && strtoupper($challan->buyerUser->state) === strtoupper(Auth::user()->state))
                        <tr>
                            <td><td><td>CGST at {{ $tax/2 }}% : {{ number_format(($netSales * $taxPercent) / 2, 2) }}</td></td></td>
                        </tr>
                        <tr>
                            <td><td><td>SGST at {{ $tax/2 }}% : {{ number_format(($netSales * $taxPercent) / 2, 2) }}</td></td></td>
                        </tr>
                    @else
                        <tr>
                            <td><td><td>IGST at {{ $tax }}% : {{ number_format($netSales * $taxPercent, 2) }}</td></td></td>
                        </tr>
                    @endif
                    @endforeach
                @elseif($totalDiscount > 0)
                    <tr>
                        <td><td><td>Total : {{ number_format($totalWithoutTax, 2) }}</td></td></td>
                    </tr>
                    <tr>
                        <td><td><td>Discount : {{ number_format($totalDiscount, 2) }}</td></td></td>
                    </tr>
                    <tr>
                        <td><td><td>Net Total : {{ number_format($totalWithoutTax - $totalDiscount, 2) }}</td></td></td>
                    </tr>
                @endif
            </table>
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <td>
                            <b>Total Qty:</b>
                            <p>{{ $challan->total_qty }}</p>
                        </td>
                    </tr>
                    @if (!empty($challan->comment))
                    <tr>
                        <td>
                            <b>Comment:</b>
                            <p>{{ $challan->comment }}</p>
                        </td>
                    </tr>
                    @endif
                    {{-- <tr>
                        <td>
                            <b>Status:</b>
                            <p>{{ $challan->status }}</p>
                        </td>
                    </tr> --}}
                    <tr class="total">
                        <td colspan="2">
                            @if ($showAmount)
                            <b><small>AMOUNT IN WORDS</small></b>
                            <br>
                            {{ numberToIndianRupees($challan->total) }}
                            @endif
                        </td>
                        <td style="width: 30%; text-align: right">
                            @if ($challan->round_off != 0)
                            <b>Round Off</b>
                                <p>&#8377; {{$challan->round_off}}</p>
                            @endif
                            <br>
                            @if ($showAmount)
                            <b>Grand Total</b>
                            &#8377; {{ $challan->total }}
                            <br>
                            @endif

                            @if ($pdfData && $pdfData->signature_option_sender === 'Signature' && isset($pdfData->signature_sender))
                                <img style="height: 70px" src="{{ Storage::disk('s3')->temporaryUrl($pdfData->signature_sender, now()->addHours(1)) }}" alt="">
                                <br>
                                <div><small style="font-size: 8px; font-weight: normal;">Authorized Signature</small></div>
                            @endif
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2">
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
                        </td>
                        <td style="width: 30%; text-align: right">
                            @if($challan->signature)
                            @php
                            $path = public_path('image/'.$challan->signature);
                            $type = pathinfo($path, PATHINFO_EXTENSION);
                            $data = file_get_contents($path);
                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            @endphp

                            <img style="height: 80px;" src="{{ $base64 }}" alt="Signature">
                            <br>
                            <small style="font-size: 8px; font-weight: normal;">Receiver's Signature   </small>
                            @endif
                        </td>
                </tbody>
            </table>
            {{-- {{dd("d")}} --}}
        </div>
        <div class="footer">
            <!-- Footer content goes here -->
            <div style="font-size: 10px;">
                @if ($pdfData && $pdfData->signature_option_sender === 'FooterStamp')
                    * This is a computer-generated {{ $pdfData->challan_heading ?? "Challan" }} and does not require a physical signature
                @endif
            </div>
             <br>
            @if(isset($pdfData->challan_stamp) && $pdfData->challan_stamp == 1)
            <img src="https://theparchi.com/image/Vector.png" alt="theparchi"> <br>

            <small>POWERED BY</small> <a href="www.theparchi.com" style="color: black;">www.TheParchi.com</a>
            @endif
        </div>

</body>

</html>
