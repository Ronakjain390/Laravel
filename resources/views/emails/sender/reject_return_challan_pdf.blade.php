<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Return Challan PDF Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #000;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
        }

     

        p {
            margin: 10px 0;
        }

        .pdf-link {
            color: #0080ff;
            text-decoration: none;
            text-align: center;
        }

        .pdf-link:hover {
            text-decoration: underline;
        }

        .challan-box {
            /* max-width: 890px; */
            margin: auto;
            padding: 2px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 12px;
            line-height: 14px;
            font-family: 'Helvetica Neue', 'DejaVu Sans', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #000;
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

        table{
          border-spacing: 0;
        }
        .challan-box table tr td:nth-child(2),
        .text-right {
            text-align: right;
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

        .information {
            width: 100%;
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
        .header{
            /* justify-content: space-between; */
            text-align: center;
            color: black;
            /* display: flex; */
            align-items: center;
        }
   
       
        tbody {
            width: fit-content;
        }
        @media only screen and (max-width: 600px) {
            h1{
                font-size: 12px;
                
            }
.table-data{
    font-size: 9px;
    white-space: nowrap;
    margin-bottom: 5px;
}
.challan-box table tr.information table {
            padding-bottom: 10px;
            font-size: 9px;
            line-height: 8px;
        }
body{
    font-size: 9px;
}
          
        }
    </style>
</head>
{{-- @dd($returnChallan); --}}
<body>

    <div class="container">
        <div class="header" >
            <h1>Delivery Return Challan : {{ $returnChallan->challan_series }}</h1>
            {{-- <a class="pdf-link" style="text-align: center" href="{{ $pdfUrl }}">Download Delivery Return Challan</a> --}}
        </div>
        
        
     

        <p> Hello, <strong>{{ucfirst($returnChallan->receiver)}}</strong> your sent Return Challan has been Rejected by  <strong> {{ ucfirst($returnChallan->sender)}} </strong></p>
        <p>Login to your <a href="https://theparchi.com/">TheParchi.com</a> account to check more details.</p> <br> 
        <table style="width: 100%;">
            <tbody>

                <tr class="information" style="width: 100%;">
                    <td colspan="2">
                        <table style="border-top: 1px solid; width: 100%;">
                            <tr>
                                <td>
                                    <b> SENDER:</b>
                                        {{ ucfirst($returnChallan->senderUser->company_name ?? $returnChallan->senderUser->name) }}
                                    <br>
                                    <b>Address:</b> {{ ucfirst($returnChallan->senderUser->address) }} <br>
                                    <b>City:</b> {{ $returnChallan->senderUser->city }} <br>
                                    <b>Pin Code:</b> {{ $returnChallan->senderUser->pincode }} <br>
                                    <b>State:</b> {{ ucfirst($returnChallan->senderUser->state) }} <br>
                                    <b>Phone:</b> +91 {{ $returnChallan->senderUser->phone }} <br>
                                    @if (isset($returnChallan->senderUser->pancard))
                                        PAN: {{ strtoupper($returnChallan->senderUser->pancard) }} <br>
                                    @endif
                                    @if (isset($returnChallan->senderUser->pancard))
                                        GSTIN: {{ strtoupper($returnChallan->senderUser->gst_number) }} <br>
                                    @endif
                                </td>
                                <td>
                                    <b> RECEIVER: </b>{{ ucfirst($returnChallan->receiverUser->company_name ?? $returnChallan->receiverUser->name) }}</b> <br>
                                    <b> Address: </b>{{ ucfirst($returnChallan->receiverUser->address) }} <br>
                                    <b>City:</b> {{ ucfirst($returnChallan->receiverUser->city) }} <br>
                                    <b>Pincode:</b> {{ ucfirst($returnChallan->receiverUser->pincode) }} <br>
                                    <b>State: </b> {{ ucfirst($returnChallan->receiverUser->state) }} <br>
                                    <b>Phone: </b>+91 {{ $returnChallan->receiverUser->phone }} <br>
                                    {{-- <b>Email: </b>{{ $returnChallan->receiverUser->email }}<br> --}}
                                </td>

                            </tr>
                        </table>

                    </td>
                </tr>

            </tbody>
        </table>
        <table style="text-align: center; width: 100%;" class="table-data">
            <thead style="background-color: #000">
                <tr class="heading" style="color: white">
                    <td>
                        S No
                    </td>

                    @foreach ($returnChallan->orderDetails[0]->columns as $column)
                        <td style="width: auto ">
                            {{ ucfirst($column->column_name) }}
                        </td>
                    @endforeach
                    <td>
                        Unit
                    </td>
                    <td>
                        Rate
                    </td>
                    <td>
                        Qty
                    </td>

                    <td>
                        Total Amount
                    </td>
                </tr>

            </thead>
            <tbody>
                @php
                    // dd($returnChallan->orderDetails);
                    $total_rate = 0;
                @endphp
                @foreach ($returnChallan->orderDetails as $index => $detail)
                    <tr class="item" style="border-bottom: 1px solid width: 100%;">
                        @php
                            $total_rate = $total_rate + $detail->rate;
                        @endphp
                        <td>
                            {{ $index + 1 }}
                        </td>
                        @foreach ($detail->columns as $column)
                            <td style="width: auto">
                                {{ strtoupper($column->column_value) }}
                            </td>
                        @endforeach
                        <td>
                            {{ $detail->unit }}
                        </td>

                        <td>
                            {{ $detail->rate }}
                        </td>
                        <td>
                            {{ $detail->qty }}
                        </td>
                        <td>
                            {{ $detail->total_amount }}
                        </td>

                    </tr>
                @endforeach
               
            </tbody>
        </table>
        <table style="width: 100%; margin-top:12px;" class="table-data">
            <thead>

                <tr>
                    <td>
                        <b>Comment:</b>
                        <p>{{ $returnChallan->comment }}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Status:</b>
                        <p>{{ $returnChallan->status ?? 'Rejected'}}</p>
                    </td>
                </tr>
                <tr class="total">
                    <td colspan="2">
                        {{-- <b>AMOUNT:</b> --}}
                        {{-- {{ numberToIndianRupees($challan->total) }} --}}
                        <br>
                        {{-- <b><small>in Words</small></b> --}}
                    </td>
                    <td style="width: 25%;">
                        <b> Grand Total </b>
                        &#8377; {{ $returnChallan->total }}
                    </td>
                </tr>
            </thead>


        </table>
        <!-- You can display the details of the Return Challan here -->
        <!-- For example: -->
        {{-- <p>Return Challan ID: {{ $returnChallan->id }}</p>
        <p>Return Challan Series: {{ $returnChallan->challan_series }}</p>
        <p>Sender: {{ $returnChallan->sender }}</p>
        <p>Receiver: {{ $returnChallan->receiver }}</p> --}}
        <!-- Add more details here based on your requirements -->

        {{-- <p>You can download the PDF from the link below:</p>
        <p><a class="pdf-link" href="{{ $pdfUrl }}">Download Delivery Return Challan</a></p>

        <p>Thank you for using our service!</p> --}}
        <div class="footer">
            <!-- Footer content goes here -->
             <br>
             <a href="https://theparchi.com/" style="color: black;"><img src="{{asset('image/Vector.png')}}" alt="theparchi"></a>
             <br>
    
            <small>POWERED BY</small> <a href="https://theparchi.com/" style="color: black; margin-top:5px;">www.TheParchi.com</a>
        </div>
    </div>
</body>

</html>
