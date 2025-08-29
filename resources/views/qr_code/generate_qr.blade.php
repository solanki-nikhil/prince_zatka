<!DOCTYPE html>
<html>

<head>
     <title>{{$title}}</title>
     <style>
          /* html body {
               font-family: Roboto, 'Segoe UI', Tahoma, sans-serif;
               font-size: 83%;
               line-height: 1.1rem;
          } */

          * {
               box-sizing: border-box;
          }

          body {
               font-family: Roboto, 'Segoe UI', Tahoma, sans-serif;
          }

          .column {
               float: left;
               width: 30%;
               padding: 0px;
               margin: 10px;
          }

          .row {
               margin: 0px;
          }

          .row:after {
               content: "";
               display: table;
               clear: both;
          }

          .card {
               box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
               padding: 16px;
               text-align: center;
               background-color: #f1f1f1;
          }
     </style>
</head>

<body>
     <div class="row">
          @foreach($qRCode as $key => $item)
          @if($key % 3 == 0)
     </div>
     <div class="row">
          @endif
          <div class="column">
               <div class="card">
                    <img src="data:image/png;base64, {!! base64_encode(QrCode::size(200)->generate($item->code)) !!}">
                    <p>{{$item->code}}</p>
               </div>
          </div>
          @endforeach
     </div>
</body>

</html>