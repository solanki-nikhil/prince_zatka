<!--Start Model Card Open-->
<div class="row" id="basic-table">
     <div class="col-12">
          <div class="card mb-0">
               <div class="table-responsive">
                    <table class="table">
                         <thead class="table-dark">
                              <tr>
                                   <th>Sr.No.</th>
                                   <th>Date</th>
                                   <th>Point</th>
                              </tr>
                         </thead>
                         <tbody>
                              @foreach($pointRedeem as $key => $item)
                              <tr>
                                   @if($item->status == '0')
                                        @php $credit = '+'; @endphp
                                        @php $class = "text-success"; @endphp
                                   @else
                                        @php $credit = '-'; @endphp
                                        @php $class = "text-danger"; @endphp
                                   @endif
                                   <td>{{$key+1}}</td>
                                   <td>{{$item->created_at->format('d-m-Y')}}</td>
                                   <td class="{{$class}}">{{$credit}} {{$item->redeem}}</td>
                              </tr>
                              @endforeach
                         </tbody>
                    </table>
               </div>
          </div>
     </div>
</div>
<!--End Model Card Open-->