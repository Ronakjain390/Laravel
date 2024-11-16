<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Note</title>
    <style>
        body {
            width: 270px;
            font-family: 'Helvetica Neue', 'DejaVu Sans', 'Helvetica', Helvetica, Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            margin-bottom: 30px;
        }

        /* .container {
            padding: 8px;
        } */

        h1 {
            text-align: center;
            font-size: 16px;
            margin: 0;
            padding: 0;
        }

        .details {
            margin: 8px 0;
        }

        .details div {
            margin-bottom: 5px;
        }

        .items {
            width: 270px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            /* margin-top: 7px; */
        }

        .items table {
            width: 270px;
            border-collapse: collapse;
        }

        .items table th,
        .items table td {
            padding: 1px;
            text-align: left;
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
            margin-bottom: 8px;
        }

        .items table th {
            border-bottom: 1px solid #000;
        }

        .items table td {
            border-bottom: 1px dashed #000;
        }

        .total {
            text-align: right;
            margin-top: 8px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container" style="width: min-content;">
        <div class="header">
            <h1>{{ empty($pdfData->challan_heading) ? 'Challan' : $pdfData->challan_heading }}</h1>
            <h1 style="font-size: smaller"> #{{ strtoupper($challan->challan_series) }}-{{ $challan->series_num }}</h1>
            <div style="text-align: center; margin-top: 5px; font-size:smaller">Date: {{ date('j-m-Y', strtotime($challan->statuses[0]->created_at)) }}</div>
        </div>
        <div class="details">
            <div class="details">
                <div> <strong>
                        Sender: {{ $challan->senderUser->company_name ? ucfirst($challan->senderUser->company_name) :
                        ucfirst($challan->senderUser->name) }}
                    </strong>
                </div>
                @if (isset($challan->senderUser->address))
                <div> Address:{{ ucfirst($challan->senderUser->address) }}, {{ $challan->senderUser->city }}, {{
                    ucfirst($challan->senderUser->state) }}, {{ $challan->senderUser->pincode }}</div>
                @endif

                @if (isset($challan->senderUser->phone))
                <div>Phone: +91{{ $challan->senderUser->phone }}</div>
                @endif
                {{-- <div style="text-align: center; margin-top: 5px;">Date: 26-06-2024</div> --}}
            </div>
            @if ($challan->receiverUser)
            <div class="details">

                <div> <strong>
                        Receiver : {{ $challan->receiverUser->company_name ?
                        ucfirst($challan->receiverUser->company_name) : ucfirst($challan->receiverUser->name) }}
                    </strong>
                </div>
                @if (isset($challan->receiverUser->address))
                <div> Address:{{ ucfirst($challan->receiverUser->address) }}, {{ $challan->receiverUser->city }}, {{
                    ucfirst($challan->receiverUser->state) }}, {{ $challan->receiverUser->pincode }}</div>
                @endif

                @if (isset($challan->receiverUser->phone))
                <div>Phone: +91{{ $challan->receiverUser->phone }}</div>
                @endif

            </div>
            @endif
        </div>
        <div class="items" style="font-size: 9px">
            <table style="font-size: smaller; width: 100%;">
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
                <thead style="font-size: smaller">
                    <tr>
                        @foreach ($columnNames as $columnName)
                            @if ($columnName != 'Hsn')
                                @if ($columnName == 'Article')
                                    <th>{{ strtoupper($columnName) }}</th>
                                @else
                                    <th style="white-space: normal; max-width: 100px;">{{ strtoupper($columnName) }}</th>
                                @endif
                            @endif
                        @endforeach
                        @if ($showRate)
                            <th style="white-space: normal; max-width: 100px;">RATE</th>
                        @endif
                        @if ($showQty)
                            <th>QTY</th>
                        @endif
                        @if ($showTax)
                            <th>TAX</th>
                        @endif
                        @if ($showAmount)
                            <th>TOTAL</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $total_rate = 0; @endphp
                    @foreach ($challan->orderDetails as $index => $detail)
                        @php $total_rate = $total_rate + floatval($detail->rate); @endphp
                        <tr>
                            @foreach ($columnNames as $columnName)
                            @php
                            $columnValue = null;
                            $hsnValue = null; // Initialize HSN value outside to make it available for all conditions
                            foreach ($detail->columns as $column) {
                                $normalizedColumnName = strtolower($column->column_name); // Normalize column name to lowercase
                                if ($normalizedColumnName == strtolower($columnName)) {
                                    $columnValue = $column->column_value;
                                }
                                if ($normalizedColumnName == 'hsn') { // Case-insensitive match for HSN
                                    $hsnValue = $column->column_value;
                                }
                            }
                            @endphp
                            @if (strtolower($columnName) == 'article')
                                <td>
                                    {{ strtoupper($columnValue) }}<br>
                                    @if(!empty($hsnValue))
                                        HSN: {{ strtoupper($hsnValue) }}
                                    @endif
                                </td>
                            @elseif (strtolower($columnName) == 'details')
                                <td style="white-space: normal; max-width: 150px; word-wrap: break-word;">{{ strtoupper($columnValue) }}</td>
                            @elseif (strtolower($columnName) != 'hsn') // Ensure HSN column is excluded based on normalized name
                                <td style="white-space: normal; max-width: 100px;">{{ strtoupper($columnValue) }}</td>
                            @endif
                        @endforeach
                            @if ($showRate)
                                <td style="white-space: normal; max-width: 100px;">
                                    @if (!is_null($detail->rate))
                                        ₹{{ $detail->rate }}

                                    @endif
                                </td>
                            @endif
                            @if ($showQty)
                            <td><span style="white-space: nowrap;">{{ $detail->qty }} {{ $detail->unit }}</span></td>
                            @endif
                            @if ($showTax)
                                <td>{{ $detail->tax }}</td>
                            @endif
                            @if ($showAmount)
                                <td>
                                    @if($detail->total_amount)
                                        ₹{{ $detail->total_amount }}
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <table style="width: 265px; font-size:10px;">
            @if(!empty($challan->comment))
            <tr>
                <td colspan="4" style="text-align: left; vertical-align: top; font-size:8px">Comment: {{ $challan->comment }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="2" style="text-align: right;">Total Qty:</td>
                <td colspan="2" style="text-align: right;">{{ $challan->total_qty }}</td>
            </tr>
            @if ($challan->round_off)
            <tr>
                <td style="text-align: right;">Round Off:</td>
                <td style="text-align: right;">₹ {{ $challan->round_off }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="2" style="text-align: right;">Total Amount:</td>
                <td colspan="2" style="text-align: right;">₹ {{ $challan->total }}</td>
            </tr>
        </table>
        <div class="footer">
            <!-- Footer content goes here -->
            <div style="font-size: 8px;">
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
