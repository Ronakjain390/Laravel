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
            font-size: 12px;
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
        <div class="challan-box">
            <div class="header_logo
      @if ($pdfData && isset($pdfData->challan_alignment)) @if ($pdfData->challan_alignment == 'center')
              center-align
          @elseif($pdfData->challan_alignment == 'left')
              left-align
          @elseif($pdfData->challan_alignment == 'right')
              right-align @endif
      @endif">
                {{-- href="{{ Storage::disk('s3')->temporaryUrl($columnName->pdf_url, now()->addMinutes(5)) }}" --}}
                {{-- @if (isset($pdfData['companyLogo']['challanTemporaryImageUrl'])) --}}
                @if ($pdfData && isset($pdfData->challan_logo_url))
                <img src="{{ Storage::disk('s3')->temporaryUrl($pdfData->challan_logo_url, now()->addHours(1)) }}"
                    alt="">
                {{-- <img src="{{ $pdfData->challan_logo_url }}"> --}}
                @endif
                {{-- <img src="image/Vector.png" alt="theparchi"> <br> --}}

                {{-- <img src="{{asset('image/Vector.png')}}" alt="theparchi"> <br> --}}
            </div>


            <div >
                <table>
                    <tbody>
                        <tr class="">
                            <td colspan="2">
                                <h1>{{ "TAX INVOICE" }}</h1>
                                Invoice No: #IN00{{ $orders->id }}
                            </td>
                            <td style="width: 30%; text-align: right; padding-top:10px">
                                <br><br>
                                Date: {{ date('j-m-Y', strtotime($orders->created_at)) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <table cellpadding="0" cellspacing="0" style="border-bottom:0px solid!;">



                <tbody>

                    <tr class="information">
                        <td colspan="2">
                            <table style="border-top: 1px solid;">
                                <tr>
                                    <td style="border-right: 1px solid #000; text-align: left; width: 50%;">
                                        {{-- <b> SENDER:</b> --}}
                                        <b> EIGHTLEAF DESIGN PRIVATE LIMITED</b>
                                        {{-- {{ ucfirst($orders->user->company_name ?? $orders->user->name) }} --}}
                                        <br>
                                        <b>Address:</b>
                                        A-194, Ground floor, SECTOR-83, PHASE-II <br>

                                        <b>City:</b> NOIDA<br>
                                        <b>Pin Code:</b> 201301<br>
                                        <b>State:</b> UTTAR PRADESH <br>
                                        <b>Phone:</b> +91 9873232926<br>
                                        <b>Email: </b> contact@theparchi.com <br>
                                        {{-- @if (isset($orders->user->pancard))
                                        PAN: {{ strtoupper($orders->user->pancard) }} <br>
                                        @endif--}}

                                        <b> GSTIN:</b> 09AAECE3300Q1Z1 <br>


                                    </td>
                                    <td style=" text-align: left;">
                                        @if (isset($orders->user->name)) <b> BILL TO </b> {{ ucfirst($orders->user->name)
                                        }}<br> @endif
                                        @if (isset($orders->user->address)) <b> Address: </b> {{
                                        ucfirst($orders->user->address) }}<br> @endif
                                        @if (isset($orders->user->city)) <b>City:</b> {{ ucfirst($orders->user->city) }}
                                        <br> @endif
                                        @if (isset($orders->user->pincode)) <b>Pincode:</b> {{
                                        ucfirst($orders->user->pincode) }} <br> @endif
                                        @if (isset($orders->user->state)) <b>State: </b> {{
                                        ucfirst($orders->user->state) }} <br> @endif
                                        @if (isset($orders->user->phone)) <b>Phone: </b>+91 {{ $orders->user->phone }}
                                        <br> @endif
                                        @if (isset($orders->user->email)) <b>Email: </b>{{ $orders->user->email }}<br>
                                        @endif
                                    </td>

                                </tr>
                            </table>

                        </td>
                    </tr>

                </tbody>
            </table>
            {{-- @dd($orders) --}}
            <table style="text-align: left;">
                <thead>
                    <tr class="heading">
                        <td>
                            #
                        </td>
                        <td>ARTICLE</td>

                        {{-- <td>
                            DETAILS
                        </td> --}}

                        <td>DETAILS</td>
                        <td>
                            PRICE
                        </td>
                        <td>
                            QTY
                        </td>
                        <td>TAX</td>
                        <td style="text-align:right ">
                            TOTAL
                        </td>
                    </tr>

                </thead>


                <tbody>

                    <tr class="item" style="border-bottom: 1px solid #000; ">

                        <td>
                            1
                        </td>
                        <td> {{$orders->plan->plan_name}} </td>

                        <td> {{$orders->plan->validity_days}} / Days </td>
                        <td> {{$orders->plan->price}} </td>
                        <td>1</td>
                        <td>18%</td>


                        <td style="text-align:right ">
                            &#8377; {{ $orders->amount }}
                        </td>

                    </tr>

                    <!-- <tr class="item">




                    <td style="">
                        {{-- {{ $orders->total_qty }} --}}
                    </td>
                    <td style="">
                        &#8377; {{ $orders->amount - ($orders->amount * 18) / 100  }}
                    </td>
                </tr> -->
                </tbody>
            </table>
            <table style="width: 100%;">
                <thead>
                    <tr class="total">
                        <td colspan="2">
                            <!-- <b><small>AMOUNT IN WORDS</small></b>
                            <br>

                            {{ numberToIndianRupees($orders->amount) }} -->
                        </td>
                        <td style="width: 30%; text-align: right;">
                            <b  >Total sale at 18%:</b>
                            &#8377; {{ $orders->amount }} <br>

                           @php
                               $userState = strtolower($orders->user->state ?? '');
                               $companyState = 'uttar pradesh';
                               $isSameState = ($userState === $companyState);
                           @endphp

                           {{-- <p style="margin-right:15px"> CGST at 9%: </p>&#8377; {{ ($orders->amount * 9) / 100 }} <br> --}}

                           {{-- <p style="margin-right:15px">SGST at 9%:</p> &#8377; {{ ($orders->amount * 9) / 100 }} --}}
                           @if($isSameState)
                               <p style="margin-right:15px">CGST at 9%:</p> &#8377; {{ ($orders->amount * 9) / 100 }}
                               <p style="margin-right:15px">SGST at 9%:</p> &#8377; {{ ($orders->amount * 9) / 100 }}
                           @else
                               <p style="margin-right:15px">IGST at 18%:</p> &#8377; {{ ($orders->amount * 18) / 100 }}
                           @endif
                        </td>
                    </tr>
                    {{-- @dd($orders->plan, $orders->amount + ($orders->amount * 18) / 100); --}}
                    <tr class="total">
                        <td colspan="2">
                            <b><small>AMOUNT IN WORDS</small></b>
                            <br>
                            {{ numberToIndianRupees($orders->amount) }}
                            {{-- {{ numberToIndianRupees(($orders->amount * 18) / 100) }} --}}
                        </td>
                        <td style="width: 30%; text-align: right;">
                            <b> Grand Total </b>
                            &#8377; {{ $orders->amount   }}
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3">
                            <table>
                                <tbody>
                                    {{-- <tr>
                                        <td style="font-size: smaller;">
                                            <b>BANK DETAILS</b>
                                            <br>

                                            <b>BANK NAME : </b> HDFC Bank
                                            <br>

                                            <b>BRANCH : </b> Baran
                                            <br>

                                            <b>ACCOUNT NO :</b> 658498962862
                                            <br>

                                            <b>IFSC CODE :</b> BARA336879
                                        </td>

                                    </tr> --}}

                                    <tr>
                                        <div>
                                            {{-- @if ($termsAndConditions && count($termsAndConditions) > 0) --}}
                                            <div style="font-size: smaller;">
                                                {{-- <b>TERMS AND CONDITIONS</b> <br> --}}
                                                {{-- @foreach ($termsAndConditions as $condition)
                                                <small>*{{ $condition->content }}</small> <br>
                                                @endforeach --}}
                                            </div>
                                            {{-- @endif --}}
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
                * This is a computer-generated invoice and does not require a physical signature
            </div> <br>
            <img src="https://theparchi.com/image/Vector.png" alt="theparchi"> <br>
            <small>POWERED BY</small> <a href="www.theparchi.com" style="color: black;">www.TheParchi.com</a>
        </div>

</body>

</html>
