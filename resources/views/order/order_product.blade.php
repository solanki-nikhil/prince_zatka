<!--Start Model Card Open-->
@if(!empty($order->remarks))
<div class="row">
    <div class="col-12">
        <div class="alert alert-danger" style="color: #d8000c; background: none; border: none; font-weight: bold; padding-left: 0;">
            Remark: {{ $order->remarks }}
        </div>
    </div>
</div>
@endif
<div class="row" id="basic-table">
     <div class="col-12">
          <div class="card mb-0">
               <div class="table-responsive">
                    <table class="table">
                         <thead class="table-dark">
                              <tr>
                                   <th>Pro.No.</th>
                                   <th>Category</th>
                                   <th>Product</th>
                                   <th>Model</th>
                                   <th>Image</th>
                                   <th>Amount</th>
                                   <th>Quantity</th>
                                   <th>Total Amount</th>
                                   @if($order->order_status != 3)
                                   <th>Action</th>
                                   @endif
                              </tr>
                         </thead>
                         <tbody>
                              @foreach($orderTransection as $key => $item)
                              <tr>
                                   <td>{{$key+1}}</td>
                                   <td>{{$item->product->category->category_name}}</td>
                                   <td>{{$item->product->product_name}}</td>
                                   <td>{{$item->product->product_code}}</td>
                                   <td>
                                        @if($item->product->image != '')
                                        <a href="{{asset('upload/product/'.$item->product->image)}}" data-fancybox="gallery_{{$item->product->product_name}}" data-caption="{{$item->product->product_name}}" class="gallary-item-overlay">
                                             <img class="mt-1 img-fluid rounded" height="35" width="35" src="{{asset('upload/product/'.$item->product->image)}}" alt="{{$item->product->product_name}}" title="{{$item->product->product_name}}">
                                        </a>
                                        @else
                                        -
                                        @endif
                                   </td>
                                   <td>{{ number_format($item->amount,2)}}</td>
                                   <td>{{$item->box}}</td>
                                   <td>{{ number_format($item->total_amount,2)}}</td>
                                   @if($order->order_status != 3)
                                   <td><a data-id="{{$item->id}}" href="javascript:void(0);" class="avatar bg-light-danger p-50 m-0 text-danger delete-single-order" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a></td>
                                   @endif
                              </tr>
                              @endforeach
                         </tbody>
                    </table>
               </div>
          </div>
     </div>
</div>
<!--End Model Card Open-->

