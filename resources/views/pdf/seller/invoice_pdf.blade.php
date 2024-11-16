<!DOCTYPE html>
<html>

<head>
    <title> Invoice</title>
    {{-- @dd($invoice); --}}
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
    {{-- @dd($invoice->buyerUser->bank_name); --}}
    {{-- @dump($invoice) --}}
            <div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                      <td colspan="2" style="text-align: left;">
                        <h1 style="margin: 0;">{{ $pdfData->invoice_heading ?? 'Invoice' }}</h1>
                      </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align: left;">
                          @if(isset($invoice->purchase_order_series) && !empty($invoice->purchase_order_series))
                            {{-- <b>PO No. {{ $invoice->purchase_order_series }}</b> --}}
                          @endif
                        </td>
                        <td style="width: 50%; text-align: right;">
                          @if(isset($invoice->eway_bill_no) && !empty($invoice->eway_bill_no))
                            <b>E-way Bill No: {{ $invoice->eway_bill_no }}</b>
                          @endif
                        </td>
                      </tr>
                    <tr>
                      <td style="width: 50%; text-align: left;">
                        <b>Invoice No. : {{ strtoupper($invoice->invoice_series) }}-{{ $invoice->series_num }}</b>
                      </td>
                      <td style="width: 50%; text-align: right;">
                        @if(isset($invoice->statuses) && count($invoice->statuses) > 0)
                          <b>Date: {{ date('d-m-Y', strtotime($invoice->invoice_date)) }}</b>
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

                                        <b>From:</b> :
                                        @if(isset($invoice->sellerUser))
                                            @if($invoice->sellerUser->company_name)
                                                {{ ucfirst($invoice->sellerUser->company_name) }}
                                            @elseif($invoice->sellerUser->name)
                                                {{ ucfirst($invoice->sellerUser->name) }}
                                            @else
                                                N/A
                                            @endif
                                        @elseif(isset($invoice->seller))
                                            {{ ucfirst($invoice->seller) }}
                                        @else
                                            N/A
                                        @endif
                                        <br>
                                        @if (isset($invoice->sellerUser->address)) <b>Address:</b> {{
                                        ucfirst($invoice->sellerUser->address) }} <br> @endif
                                        @if (isset($invoice->sellerUser->city)) <b>City:</b> {{
                                        $invoice->sellerUser->city }} <br> @endif
                                        @if (isset($invoice->sellerUser->pincode)) <b>Pin Code:</b> {{
                                        $invoice->sellerUser->pincode }} <br> @endif
                                        @if (isset($invoice->sellerUser->state)) <b>State:</b> {{
                                        ucfirst($invoice->sellerUser->state) }} <br> @endif
                                        @if (isset($invoice->sellerUser->phone)) <b>Phone:</b> +91 {{
                                        $invoice->sellerUser->phone }} <br> @endif
                                        @if (isset($invoice->sellerUser->pancard))
                                        <b>PAN:</b> {{ strtoupper($invoice->sellerUser->pancard) }} <br>
                                        @endif
                                        @if (isset($invoice->sellerUser->gst_number))
                                        <b>GSTIN:</b> {{ strtoupper($invoice->sellerUser->gst_number) }} <br>
                                        @endif
                                    </td>
                                    <td style="border-right: 1px solid gray; text-align: left;">


                                        @if (isset($invoice->buyerUser) && $invoice->buyerUser)
                                            @if (isset($invoice->buyerUser->name))
                                                <b>Bill To:</b> {{ ucfirst($invoice->buyerUser->name) }}<br>
                                            @else
                                                <b>Bill To:</b> N/A<br>
                                            @endif
                                        @elseif (isset($invoice->buyer) && $invoice->buyer)
                                            <b>Bill To:</b> {{ ucfirst($invoice->buyer) }}<br>
                                        @else
                                            <b>Bill To:</b> N/A<br>
                                        @endif                                        @if (isset($invoice->buyerUser->address) && !empty($invoice->buyerUser->address)) <b> Address: </b>{{
                                        ucfirst($invoice->buyerUser->address) }}<br> @endif
                                        @if (isset($invoice->buyerUser->city) && !empty($invoice->buyerUser->city)) <b>City:</b> {{
                                        ucfirst($invoice->buyerUser->city) }} <br> @endif
                                        @if (isset($invoice->buyerUser->pincode) && !empty($invoice->buyerUser->pincode)) <b>Pincode:</b> {{
                                        ucfirst($invoice->buyerUser->pincode) }} <br> @endif
                                        @if (isset($invoice->buyerUser->state) && !empty($invoice->buyerUser->state)) <b>State:</b> {{
                                        ucfirst($invoice->buyerUser->state) }} <br> @endif
                                        @if (isset($invoice->buyerUser->phone) && !empty($invoice->buyerUser->phone)) <b>Phone: </b>+91 {{
                                        $invoice->buyerUser->phone }} <br> @endif
                                        @if (isset($invoice->buyerUser->email) && !empty($invoice->buyerUser->email)) <b>Email: </b>{{
                                        $invoice->buyerUser->email }}<br>
                                        @endif
                                        @if (isset($invoice->buyerUser->gst_number) && !empty(trim($invoice->buyerUser->gst_number)))
                                            <b>GSTIN:</b> {{ strtoupper($invoice->buyerUser->gst_number) }} <br>
                                        @endif
                                    </td>
                                    @if (isset($invoice->buyerUser->address))
                                    <td style="text-align: left;">
                                        <b> SHIP TO </b> <br>
                                        @if (isset($invoice->buyerUser->address) && !empty($invoice->buyerUser->address)) <b> Address: </b>{{
                                        ucfirst($invoice->buyerUser->address) }}<br> @endif
                                        @if (isset($invoice->buyerUser->city) && !empty($invoice->buyerUser->city)) <b>City:</b> {{
                                        ucfirst($invoice->buyerUser->city) }} <br> @endif
                                        @if (isset($invoice->buyerUser->pincode) && !empty($invoice->buyerUser->pincode)) <b>Pincode:</b> {{
                                        ucfirst($invoice->buyerUser->pincode) }} <br> @endif
                                        @if (isset($invoice->buyerUser->state) && !empty($invoice->buyerUser->state)) <b>State:</b> {{
                                        ucfirst($invoice->buyerUser->state) }} <br> @endif
                                        @if (isset($invoice->buyerUser->phone) && !empty($invoice->buyerUser->phone)) <b>Phone: </b>+91 {{
                                        $invoice->buyerUser->phone }} <br> @endif
                                        @if (isset($invoice->buyerUser->email) && !empty($invoice->buyerUser->email)) <b>Email: </b>{{
                                        $invoice->buyerUser->email }}<br> @endif
                                        @if (isset($invoice->buyerUser->gst_number) && !empty(trim($invoice->buyerUser->gst_number)))
                                        <b>GSTIN:</b> {{ strtoupper($invoice->buyerUser->gst_number) }} <br>
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
                            foreach ($invoice->orderDetails as $detail) {
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
                            foreach ($invoice->orderDetails as $detail) {
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
                    @foreach ($invoice->orderDetails as $index => $detail)
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

            <table style="width: 100%; border-bottom:1px solid!;  text-align: right; font-size: xx-small; line-height: normal;" >

                @php
                $taxTotals = []; // Array to store totals for each unique tax value
                $totalWithoutTax = 0;
                $totalDiscount = 0;
                @endphp

                @foreach($invoice->orderDetails as $index => $orderDetail)
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
                    @if(isset($invoice->buyerUser->state) && isset(Auth::user()->state) && strtoupper($invoice->buyerUser->state) === strtoupper(Auth::user()->state))
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

            <table style="width: 100%; margin-top: 5px;">
                <thead>
                    @if($invoice->estimate_series)
                    <tr>
                        <td>
                            <b>Estimate No:</b>
                            <p>{{ $invoice->estimate_series }}</p>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td>
                            <b>Total Qty:</b>
                            <p>{{ $invoice->total_qty }}</p>
                        </td>
                    </tr>
                    @if (!empty($invoice->comment))
                    <tr>
                        <td colspan="2">
                            <b>Comment:</b>
                            <p>{{ $invoice->comment }}</p>
                        </td>
                    </tr>
                    @endif

                    <tr class="total">
                        <td colspan="2">
                            @if ($showAmount)
                            <b><small>AMOUNT IN WORDS</small></b>
                            <br>
                            {{ numberToIndianRupees($invoice->total) }}
                            @endif
                        </td>
                        <td style="width: 30%; text-align: right;">
                            @if ($invoice->round_off != 0)
                            <b>Round Off</b>
                                <p>&#8377; {{$invoice->round_off}}</p>
                            @endif
                            <br>
                            @if ($showAmount)
                            <b> Grand Total </b>
                            &#8377; {{ $invoice->total }}
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
                                            @if (isset($invoice->sellerUser->bank_name))
                                            <b>BANK DETAILS</b>
                                            <br>
                                            <b>BANK NAME : </b> {{$invoice->sellerUser->bank_name}}
                                            <br>
                                            <b>BRANCH : </b> {{$invoice->sellerUser->branch_name}}
                                            <br>
                                            <b>ACCOUNT NO :</b> {{$invoice->sellerUser->bank_account_no}}
                                            <br>
                                            <b>IFSC CODE :</b> {{$invoice->sellerUser->ifsc_code}}
                                            @endif
                                        </td>
                                    </tr>
                                    @if (isset($invoice->sellerUser->bank_name) && ($termsAndConditions && count($termsAndConditions) > 0))
                                    <tr>
                                        <td>
                                            <hr style="border-top: 1px solid #000; margin: 10px 0;">
                                        </td>
                                    </tr>
                                    @endif
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
                * This is a computer-generated {{ $pdfData->invoice_heading ?? 'Invoice' }} and does not require a physical signature
                @endif
            </div>  --}}
            <div style="font-size: 10px;">
                @if ($pdfData && $pdfData->signature_option_seller === 'FooterStamp')
                    * This is a computer-generated {{ $pdfData->invoice_heading ?? 'Invoice' }} and does not require a physical signature
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
