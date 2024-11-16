<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit/Cash Memo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            margin-bottom: 30px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 20px;
            box-sizing: border-box;
        }

        .header {
            display: flex;
            width: 100%;
            align-items: center;
            margin-bottom: 10px;
        }

        .header img {
            max-width: 60px;
            height: auto;
            margin-right: 10px;
        }

        .header-text {
            flex-grow: 1;
            text-align: right;
        }

        .header-text h2 {
            margin: 0;
            font-size: 10px;
        }

        .header-text p {
            margin: 5px 0;
            font-size: 10px;
        }

        .info {
            margin-bottom: 10px;
            text-align: center;
        }

        .info p {
            margin: 5px 0;
            font-size: 14px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 1px solid #000;
        }

        .table th, .table td {
            padding: 5px;
            text-align: left;
            border: 1px solid #000;
            font-size: 14px;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .amount-header {
            /* writing-mode: vertical-lr;
            transform: rotate(180deg); */
        }

        .total-cell {
            text-align: center;
            padding: 0;
        }

        .total-box {
            display: inline-block;
            border: 1px solid #000;
            padding: 5px;
            /* transform: rotate(180deg);
            writing-mode: vertical-lr; */
        }

        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 14px;
        }

        .footer {
            /* position: fixed; */
            /* width: 100%; */
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

        @media (max-width: 480px) {
            .table th, .table td {
                padding: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">


        <table style="width: 100%;">
            <thead>

                <tr class="total" style="font-size: 12px">
                    <td colspan="2">
                        <p><small>  @if ($pdfData && isset($pdfData->challan_logo_url))
                            <img style="height: 80px" src="{{ Storage::disk('s3')->temporaryUrl($pdfData->challan_logo_url, now()->addHours(1)) }}"
                                alt="">
                            {{-- <img src="{{ $pdfData->challan_logo_url }}"> --}}
                            @endif</p>
                        {{-- <br> --}}

                    <td style="width: 40%;/* text-align:end; */text-align: right;">
                        <p>  @if (isset($challan->senderUser->gst_number)) <b>GSTIN:</b> {{
                            ucfirst($challan->senderUser->gst_number) }} <br> @endif
                          @if (isset($challan->senderUser->phone)) <b>Mob. No.:</b> {{
                            ucfirst($challan->senderUser->phone) }} <br> @endif </p>


                    </td>
                </tr>
            </thead>


        </table>
        <div class="info">
            {{-- <p>INDIAN OIL</p> --}}
            <p>{{ empty($pdfData->challan_heading) ? "CHALLAN" : strtoupper($pdfData->challan_heading) }}</p>
            {{-- <p>{{ strtoupper($challan->challan_series) }}-{{ $challan->series_num }}</p> --}}
            <p><b>{{ strtoupper($challan->senderUser->company_name)}}</b></p>
            <p>@if (isset($challan->senderUser->address))   {{
                ucfirst($challan->senderUser->address) }}   @endif</p>

        </div>

        {{-- <div class="signature">
            <div class="row">
                <div class="col-md-6 col-span">
                    <span style="text-align: right !important">
                        @if(isset($challan->statuses[0]))
                          <b>  Date:</b> {{ date('j-m-Y', strtotime($challan->statuses[0]->created_at)) }}
                        @endif
                    </span>
                </div>
                <div class="col-md-6">
                    <span>
                        @if (isset($challan->receiverUser->name))
                            <b>RECEIVER</b> {{ ucfirst($challan->receiverUser->name) }}
                        @elseif (isset($challan->receiver))
                            <b>RECEIVER</b> {{ ucfirst($challan->receiver) }}
                        @else
                            <b>RECEIVER</b> Default
                        @endif
                    </span>
                </div>

            </div>
        </div> --}}
        <table style="width: 100%;">
            <thead>

                <tr class="total" style="font-size: 12px">
                    <td>
                        <p><small> @if (isset($challan->receiverUser->name))
                            <b>To : </b> {{ ucfirst($challan->receiverUser->name) }}
                        @elseif (isset($challan->receiver))
                            <b>To : </b> {{ ucfirst($challan->receiver) }}
                        @else
                            <b>To : </b> Default
                        @endif</small></p>
                    </td>
                    <td style="width: 33%; text-align: center">
                        <p> @if(isset($challan->challan_series))
                            <b>#</b> {{ $challan->challan_series }}-{{ $challan->series_num }}
                          @endif </p>
                    </td>
                    <td style="width: 33%; text-align: right">
                        <p>  @if(isset($challan->statuses[0]))
                            <b>  Date:</b> {{ date('j-m-Y', strtotime($challan->statuses[0]->created_at)) }}
                          @endif </p>
                    </td>
                </tr>
            </thead>


        </table>

        <table class="table">
            @php
                $total_rate = 0;
            @endphp

            <tr>
                <th>Article</th>
                <th>Rate</th>
                <th>Qty</th>
                <th class="amount-header">Amount<br>
                    {{-- Rs.&nbsp;&nbsp;&nbsp;&nbsp;p. --}}
                </th>
            </tr>

            @foreach ($challan->orderDetails as $index => $detail)
                @php
                    $total_rate += $detail->rate;
                @endphp
                <tr>
                    <td>
                        @foreach ($detail->columns as $column)
                            @if ($column->column_value)
                                {{ ucfirst($column->column_value) }} <br>
                            @endif
                        @endforeach
                    </td>
                    <td> {{ $detail->rate }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td> {{ $detail->total_amount }}</td>
                </tr>
            @endforeach

            <tr>
                <td class="total-cell">
                    {{-- <div class="total-box" style="width: 70%;">
                        <span>No.</span>
                    </div> --}}
                </td>
                <td class="total-cell">
                    <span>Total</span>
                </td>
                <td>{{ $challan->total_qty }}</td>
                @if ($challan->round_off)
                <td>{{ $challan->round_off }}</td>
                @endif
                <td> {{ $challan->total }}</td>
            </tr>
        </table>
        <div class="signature">
            <span>{{ $challan->comment }}</span>
            {{-- <span>E.&amp;O.E.</span>
            <span style="text-align: right">Signature</span> --}}
        </div>
        {{-- <p>Thank you, Visit again</p> --}}
        <div class="footer" style="font-size: 12px; margin-top:5px">
            <!-- Footer content goes here -->
            <div style="font-size: 10px;">
                @if (!$pdfData || !isset($pdfData->signature_sender))
                    * This is a computer-generated {{ $pdfData->challan_heading ?? "Challan"}} and does not require a physical signature
                @endif
            </div> <br>
            @if(isset($pdfData->challan_stamp) && $pdfData->challan_stamp == 1)
            <img style="height: 23px;" src="https://theparchi.com/image/Vector.png" alt="theparchi"> <br>

            <small>POWERED BY</small> <a href="www.theparchi.com" style="color: black;">www.TheParchi.com</a>
            @endif
        </div>
    </div>
</body>
</html>
