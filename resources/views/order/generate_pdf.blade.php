<!DOCTYPE html>
<html>
<head>
    <title>{{$title}}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #f37021;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top;
            padding: 10px;
        }
        .logo {
            width: 100px;
            height: auto;
        }
        .company-info {
            text-align: right;
            font-size: 14px;
            line-height: 1.5;
        }
        .bill-to {
          border-collapse: collapse;
          width: 100%;
            margin-top: 20px;
            padding-bottom: 10px;
        }
        .bill-to-info {
          border: none;
            font-size: 14px;
            line-height: 1.5;
        }
        .table-container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f37021;
            color: white;
        }
        .summary {
          border-collapse: collapse;
          width: 100%;
     
            margin-top: 20px;
            text-align: right;
        }
        .summary td {
          border: none;
            padding: 5px;
        }
        footer {
          padding: 5px;
          margin-top: 40px;
          }
    </style>
</head>
<body>
    <div class="invoice-container">
        <table class="header-table">
            <tr>
                <td>
                    <img src="{{ $base64 }}" class="logo" alt="Company Logo">
                </td>
                <td class="company-info">
                    <strong>Prince Zatka Machine</strong><br>
                    Plot. 21/5 Gautum Buddh<br>
                    B/h, Linova Pumps, Sd-3<br>
                    Rajkot, Gujarat 360004<br>
                    Phone: +91 76980 06300<br>
                    Email: info@princezatka.com
                </td>
            </tr>
        </table>

        <h2 style="text-align: center;">INVOICE</h2>

        <table class="bill-to" >
          <tr>
               <td style="font-weight: bold; width: 80px; text-align:center; vertical-align: top; border: none;">Bill To:</td>
               <td class="bill-to-info">
                    {{$order->name}}<br>
                    {{$order->mobile}}<br>
                    {!! nl2br(e($order->address)) !!}
               </td>
          </tr>
          </table>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderTransection as $key => $value)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$value->product->product_name}}</td>
                        <td>{{$value->box}}</td>
                        <td>{{number_format($value->amount, 2)}}</td>
                        <td>{{number_format($value->total_amount, 2)}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <table class="summary">
          <tr>
               <td style="width: 50%;"></td>
               <td style="text-align: right; width: 50%;">Total Quantity:</td>
               <td style="text-align: left;">{{ $order->total_box }}</td>
          </tr>
          <tr>
               <td style="width: 50%;"></td>
               <td style="text-align: right; width: 50%;">Total Amount:</td>
               <td style="text-align: left;">{{number_format($order->total_amount, 2)}}</td>
          </tr>
          </table>

        <footer>
          <div style="padding: 0px 5px 0px 5px;">
               <p style="text-align:right;">
                    For <b>Price Zatka Machine</b>
                    <br />
                    <br />
                    <br />
                    <br />
                    Authorised Signatory
               </p>
               <p style="text-align: center; font-size: 11px;; width: 100%; margin: 5px 0;">
                    *This is a Computer Generated Invoice
               </p>

          </div>
     </footer>
    </div>
</body>
</html>
