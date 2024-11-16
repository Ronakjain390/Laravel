<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $heading }}</title>
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
            <h1>{{ $heading }}</h1>
            <p>{{ $dynamicText }}</p>
        <a href="{{ $downloadLink }}" style="display: inline-block; padding: 10px 20px; font-size: 16px; color: #fff; background-color: #007bff; text-decoration: none; border-radius: 5px;">Download</a>
        </div>



        {{-- <p>Thank you for using our service!</p>  --}}
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
