<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Note</title>
    <style>


        body {
            width: 60mm;
            font-family: 'Helvetica Neue', 'DejaVu Sans', 'Helvetica', Helvetica, Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
            margin-right: 2px;
            margin-bottom: 30px;
        }

        /* .container {
            padding: 1px;
        } */

        h1 {
            text-align: center;
            font-size: 18px;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: right;
            margin-bottom: 10px;
        }

        .header p {
            margin: 0;
            font-size: 12px;
        }

        .details {
            margin-bottom: 10px;
            font-size: small;
            text-align: left;
            border-bottom: dashed 1px #000;
        }

        /* .details div {
            margin-bottom: 5px;
        } */
        .details div strong {
            font-weight: bold;
        }

        .items {
            width: 100%;
            margin-top: 5px;
            font-size: small;
        }

        .items table {
            width: 100%;
            border-collapse: collapse;
        }

        .items table th,
        .items table td {
            /* padding: 5px; */
            text-align: left;
        }

        /* .items table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        } */
        .items table td {
            font-size: x-small;
        }
        .total {
            text-align: right;
            margin-top: 10px;
            font-weight: bold;
            border-bottom: dashed 1px #000;
        }

        .value {
            text-align: right;
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
            /* width: 700px; */
            /* margin: 8px; */
            /* margin-bottom: 10px; */
            /* border: 1px solid #ccc; */
            /* padding: 10px; */
        }
    </style>
</head>

<body>
    <div class="container">
       {{-- @dd($challan) --}}
        <div class="header">
            <h1>{{ empty($pdfData->challan_heading) ? 'Challan' : $pdfData->challan_heading }}</h1>
            <h1 style="font-size: smaller"> #{{ strtoupper($challan->challan_series) }}-{{ $challan->series_num }}</h1>
            <div style="text-align: center; margin-top: 5px; font-size:smaller">Date: {{ date('j-m-Y', strtotime($challan->statuses[0]->created_at)) }}</div>
        </div>
        <div class="details">
            <div> <strong>
                Sender: {{ $challan->senderUser->company_name ? ucfirst($challan->senderUser->company_name) : ucfirst($challan->senderUser->name) }}
            </strong>
            </div>
            @if (isset($challan->senderUser->address))
                <div> Address:{{ ucfirst($challan->senderUser->address) }}, {{ $challan->senderUser->city }}, {{ ucfirst($challan->senderUser->state) }}, {{ $challan->senderUser->pincode }}</div>
            @endif

            @if (isset($challan->senderUser->phone))
                <div>Phone: +91{{ $challan->senderUser->phone }}</div>
            @endif
            {{-- <div style="text-align: center; margin-top: 5px;">Date: 26-06-2024</div> --}}
        </div>
        @if ($challan->receiverUser)
        <div class="details">
            <div> <strong>
                Receiver :  {{ $challan->receiverUser->company_name ? ucfirst($challan->receiverUser->company_name) : ucfirst($challan->receiverUser->name) }}
            </strong>
            </div>
            @if (isset($challan->receiverUser->address))
                <div> Address:{{ ucfirst($challan->receiverUser->address) }}, {{ $challan->receiverUser->city }}, {{ ucfirst($challan->receiverUser->state) }}, {{ $challan->receiverUser->pincode }}</div>
            @endif

            @if (isset($challan->receiverUser->phone))
                <div>Phone: +91{{ $challan->receiverUser->phone }}</div>
            @endif
        </div>
        @endif
        <div class="items">
            <table>
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

                @foreach ($challan->orderDetails as $index => $detail)
                <tbody style="border-bottom: dashed 1px #000;">
                    <tr>
                            @foreach ($columnNames as $columnName)
                                <td>{{ strtoupper($columnName) }}</td>
                                @php $total_rate = 0; @endphp
                                @php $total_rate = $total_rate + floatval($detail->rate); @endphp
                                @php
                                    $columnValue = null;
                                    foreach ($detail->columns as $column) {
                                        if ($column->column_name == $columnName) {
                                            $columnValue = $column->column_value;
                                            break;
                                        }
                                    }
                                @endphp
                                <td style="text-align: right;">{{ strtoupper($columnValue) }}</td>
                    </tr>

                        @endforeach
                        <tr>
                            @if ($showUnit)
                                <td>UNIT</td>
                                <td style="text-align: right;">{{ $detail->unit }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if ($showRate)
                                <td>RATE</td>
                            @endif
                            <td style="text-align: right;">
                                @if (!is_null($detail->rate))
                                    &#8377; {{ $detail->rate }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            @if ($showQty)
                                <td>QTY</td>
                            @endif
                            @if ($showQty)
                                <td style="text-align: right;">{{ $detail->qty }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if ($showTax)
                                <td>TAX</td>
                            @endif
                            @if ($showTax)
                                <td style="text-align: right;">{{ $detail->tax }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if ($showAmount)
                                <td>TOTAL</td>
                            @endif
                            <td style="text-align: right">
                                @if ($detail->total_amount)
                                    &#8377; {{ $detail->total_amount }}
                                @endif
                            </td>
                        </tr>
                    </tbody>
                    @endforeach

            </table>
        </div>
        <table style="width: 100%; font-size:smaller;">
            <tr>
                <td style="text-align: right;">Total Qty:</td>
                <td style="text-align: right;">₹ {{ $challan->total_qty }}</td>
            </tr>
            @if ($challan->round_off)
            <tr>
                <td style="text-align: right;">Round Off:</td>
                <td style="text-align: right;">₹ {{ $challan->round_off }}</td>
            </tr>
            @endif
            <tr>
                <td style="text-align: right;">Total Amount:</td>
                <td style="text-align: right;">₹ {{ $challan->total }}</td>
            </tr>
        </table>
        <br>
        <div class="footer">
            <!-- Footer content goes here -->
            <div style="font-size: 10px;">
                @if (!$pdfData || !isset($pdfData->signature_sender))
                    * This is a computer-generated {{ $pdfData->challan_heading ?? "Challan"}} and does not require a physical signature
                @endif
            </div> <br>
            @if(isset($pdfData->challan_stamp) && $pdfData->challan_stamp == 1)
            <img src="https://theparchi.com/image/Vector.png" alt="theparchi"> <br>

            <div style="font-size: small; display: inline-block; white-space: nowrap;" >
                <small style="font-size: x-small;">POWERED BY</small>
                <a href="http://www.theparchi.com" style="color: black; text-decoration: none; margin-top:2px;">www.TheParchi.com</a>
            </div>
            @endif
        </div>
        {{-- @dd($challan->orderDetails) --}}
    </div>
</body>

</html>
