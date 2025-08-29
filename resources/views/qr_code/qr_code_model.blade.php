<!--Start Model Card Open-->
<div class="row" id="basic-table">
     <div class="col-12">
          <div class="card mb-0">
               <div class="table-responsive">
                    <table class="table">
                         <thead class="table-dark">
                              <tr>
                                   <th>Sr.No.</th>
                                   <th>Code</th>
                                   <th>Point</th>
                                   <th>Action</th>
                              </tr>
                         </thead>
                         <tbody>
                              @foreach($qRCode as $key => $item)
                              <tr>
                                   <td>{{$key+1}}</td>
                                   <td>{{$item->code}}</td>
                                   <td>{{$item->amount}}</td>
                                   <td><a href="{{route('qr-code.edit', $item->id)}}" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>
                                        <a data-id="{{$item->id}}" href="javascript:void(0);" class="avatar bg-light-danger p-50 m-0 text-danger delete-qrcode" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>
                                   </td>
                              </tr>
                              @endforeach
                         </tbody>
                    </table>
               </div>
          </div>
     </div>
</div>
<!--End Model Card Open-->