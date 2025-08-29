<!DOCTYPE html>
<html>

<head>
     <title>{{$title}}</title>
     <style>
          html body {
               font-family: Roboto, 'Segoe UI', Tahoma, sans-serif;
               font-size: 83%;
               line-height: 1.1rem;
          }

          h4 {
               text-align: center;
          }

          table {
               width: 100%;
               border: 1px solid #000;
               border-spacing: 0;
          }

          #order,
          tr,
          th,
          td {
               text-align: left !important;
               padding-left: 1px;
          }

          #order_item .bg-head {
               background-color: #d7d7d7;
               border: none;
          }

          a {
               text-decoration: none;
               color: #000;
          }

          footer {
               position: fixed;
               bottom: -60px;
               left: 0px;
               right: 0px;
               height: 120px;
          }
     </style>
</head>

<body>
     <h4>Invoice</h4>
     <table id="order">
          <tr>
               <th>Order ID</th>
               <td>{{$order->order_id}}</td>
               <th colspan="2">Ambition Pipe</th>
          </tr>
          <tr>
               <th>Bill NO.</th>
               <td> @if(!is_null($order->bill_number))
                    {{$order->bill_number}}
                    @else
                    -
                    @endif
               </td>
               <td colspan="2" rowspan="2">Plot No. 11, Shubh Ind. Zone, Nr. Makhavad Chowkdi,<br>
                    Khambha, Ta. Lodhika, Dist. Rajkot.</td>
          </tr>
          <tr>
               <th>Date</th>
               <td>{{$order->created_at->format('d-m-Y')}}</td>
          </tr>
          <tr>
               <th>Customer Name</th>
               <td>{{$order->name}}</td>
               <td colspan="2"><a href="tel:8000099081"><b>Viraj Patel</b> +91 80000 99081</a></td>
          </tr>
          <tr>
               <th>Customer NO.</th>
               <td>{{$order->mobile}}</td>
               <td colspan="2"><a href="mailto:ambitionpipes19@gmail.com">ambitionpipes19@gmail.com</a></td>
          </tr>
          <tr>
               <th>Name</th>
               <td>{{$order->name}}</td>
               <td colspan="2">Price Zatka Machine</td>
          </tr>
          <tr>
               <th>Number</th>
               <td>{{$order->mobile}}</td>
               <td colspan="2"></td>
          </tr>
          <tr>
               <th>Address</th>
               <td>{!! wordwrap($order->address, 30,'<br />') !!}</td>
          </tr>
     </table>
     <table style="margin-top: 5px;" id="order_item">
          <thead class="bg-head">
               <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Amount</th>
                    <th>Quantity</th>
                    <th>Discount(%)</th>
                    <th>Total Amount</th>
               </tr>
          </thead>
          <tbody>
               @foreach($order->orderTransection as $key => $value)
               <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$value->product->product_name}}</td>
                    <td>{{$value->amount}}</td>
                    <td>{{$value->pices}}</td>
                    <td>{{$value->discount}}</td>
                    <td>{{number_format($value->total_amount,2)}}</td>
               </tr>
               @endforeach
               <tr>
                    <td colspan="6" style="border-bottom: 1px solid #000;"></td>
               </tr>
               <tr>
                    <td colspan="2"></td>
                    <th>Total Quantity</th>
                    <td>{{$order->total_pices}}</td>
                    <th>Total Amount</th>
                    <td>{{number_format($order->total_amount,2)}}</td>
               </tr>
          </tbody>
     </table>

     <footer>
          <div style="padding: 0px 5px 0px 5px;">
               <p style="text-align:right;">
                    For <b>Price Zatka Machine</b>
                    <br />
                    <br />
                    Authorised Signatory

               </p>
               <p style="text-align: center"> *This is a Computer Generated Invoice </p>
          </div>
     </footer>
</body>

</html>