@extends('layouts.app')
@section('title', 'Stock')
@section('content')
<div class="app-content content ">
     <div class="content-overlay"></div>
     <div class="header-navbar-shadow"></div>
     <div class="content-wrapper container-xxl p-0">
          <div class="content-body">
               <section id="multiple-column-form">
                    <div class="row">
                         <div class="col-12">
                              <div class="card">
                                   <div class="card-header">
                                   @if((isset($stockMaster) && isset($stockMaster->id)))
                                   <h3 class="card-title">Edit <small>Stock</small></h3>
                                   @else
                                   <h3 class="card-title">Add <small>Stock</small></h3>
                                   @endif
                                   </div>
                                   <div class="card-body">
                                        <form id="stock_master_form" class="form" action="javascript:void(0);" method="POST">
                                             <div class="row">
                                                  @if((isset($stockMaster) && isset($stockMaster->id)))
                                                  <input type="hidden" name="stock_id" value="{{ $stockMaster->id }}">
                                                  @endif
                                                  <div class="col-md-3 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="category_id">Category<span class="text-danger">*</span></label>
                                                       <select class="form-select" name="category_id" id="category_id">
                                                            <option value="" selected disabled>Select Category</option>
                                                            @foreach ($category as $item)
                                                            <option value="{{ $item->id }}" {{ (isset($stockMaster) && $stockMaster->category_id == $item->id ) ? 'selected' : '' }}>{{ $item->category_name}}</option>
                                                            @endforeach
                                                       </select>
                                                       <span class="invalid-feedback d-block" id="error_country_id" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-3 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="sub_category_id">Sub Category<span class="text-danger">*</span></label>
                                                       <select class="form-select" name="sub_category_id" id="sub_category_id">
                                                            <option value="" selected disabled>Select Sub Category</option>
                                                            @if((isset($stockMaster) && isset($stockMaster->id)))
                                                                 @foreach ($subCategory as $item)
                                                                 <option value="{{ $item->id }}" {{ (isset($stockMaster) && $stockMaster->sub_category_id == $item->id ) ? 'selected' : '' }}>{{ $item->sub_category_name}}</option>
                                                                 @endforeach
                                                            @endif
                                                       </select>
                                                  </div>
                                                  <div class="col-md-3 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="product_id">Product<span class="text-danger">*</span></label>
                                                       <select class="form-select" name="product_id" id="product_id">
                                                            <option value="" selected disabled>Select Product</option>
                                                            @if((isset($stockMaster) && isset($stockMaster->id)))
                                                                 @foreach ($product as $item)
                                                                 <option value="{{ $item->id }}" {{ (isset($stockMaster) && $stockMaster->product_id == $item->id ) ? 'selected' : '' }}>{{ $item->product_name}} : {{ $item->quantity}}</option>
                                                                 @endforeach
                                                            @endif
                                                       </select>
                                                  </div>
                                                  {{--<div class="col-md-6 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="product_id">Product<span class="text-danger">*</span></label>
                                                       <select class="form-select" name="product_id" id="product_id">
                                                            <option value="" selected>Select Product</option>
                                                            @foreach ($product as $item)
                                                            <option value="{{ $item->id }}" {{ (isset($stockMaster) && $stockMaster->product_id == $item->id ) ? 'selected' : '' }}>{{ $item->product_name}} - {{ $item->quantity}}</option>
                                                            @endforeach
                                                       </select>
                                                       <span class="invalid-feedback d-block" id="error_category_id" role="alert"></span>
                                                  </div>--}}
                                                  <div class="col-md-3 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="quantity">Quantity<span class="text-danger">*</span></label>
                                                       <input type="number" min="1" class="form-control" name="quantity" id="quantity" placeholder="Quantity" value="{{ ((isset($stockMaster) && isset($stockMaster->quantity)) ? $stockMaster->quantity : '')  }}">
                                                  </div>
                                             <!-- </div>
                                             <div class="row"> -->
                                                  <div class="col-md-12 col-12 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right save-stock-master">Submit</button>
                                                  </div>
                                             </div>
                                        </form>
                                   </div>
                              </div>
                         </div>
                    </div>
               </section>
          </div>
     </div>
</div>
@endsection

@section('pagescript')
<script type="text/javascript">
     $.ajaxSetup({
          headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
     });

     $(document).ready(function() {
          $('.save-stock-master').on('click', function() {
               var formData = new FormData($("#stock_master_form")[0]);
               if ($("#quantity").val() != "" && $("#product_id").val() != "" && $("#quantity").val() >= 1) {
                    $.ajax({
                         type: "POST",
                         url: "{{route('stock-master.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $("#error_stock_name").html(' ');
                              $(".save-stock-master").html('<span class="spinner-border spinner-border-sm" role="Stock" aria-hidden="true"></span> Wait');
                              $(".save-stock-master").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".save-stock-master").html('Submit');
                              $(".save-stock-master").attr('disabled', false);
                              if (response.Stock == false) {
                                   $.each(response.errors, function(key, value) {
                                        $('#error_' + key).html('<p class="text-danger mb-0">' + value + '</p>');
                                   });
                                   toastr.warning('Please Input Propper Data.', 'Warning');
                              } else if (response.server_error && response.Stock == true) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#stock_master_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#stock_master_form").validate({
                         rules: {
                              quantity: {
                                   required: true,
                                   minlength:1
                              },
                              product_id: {
                                   required: true,
                              },
                         },
                         messages: {
                              quantity: {
                                   required: "Enter Quantity",
                                   minlength:"Please Input 1 or greater Number"
                              },
                              product_id: {
                                   required: "Select Product"
                              },
                         },
                         errorElement: "p",
                         errorClass: "text-danger mb-0",

                         highlight: function(element) {
                              $(element).addClass('has-error');
                         },
                         unhighlight: function(element) {
                              $(element).removeClass('has-error');
                         },
                         errorPlacement: function(error, element) {
                              $(element).closest('.custom-input-group').append(error);
                         }
                    });
               }
          });
     });

     $(document).ready(function() {
          var product_id = $("#product_id").val();
          if (product_id != "") {
               changeCat();
          }
          $("#category_id").on('change', function() {
               changeCat();
          });

          $("#sub_category_id").on('change', function() {
               changeSub();
          });
     });

     function changeCat() {
          var id = $("#category_id").val();
          var route = "{{route('product.show','id')}}".replace('id', id);
          $.ajax({
               type: 'get',
               url: route,
               datatype: 'json',
               data: {
                    "id": id,
               },
               success: function(response) {
                    $("#sub_category_id").empty('');
                    $.each(response, function(i, value) {
                         $("#sub_category_id").append('<option value="' + value.id + '">' + value.sub_category_name + '</option>');
                    });
               changeSub();

               }
          });
     }

     function changeSub() {
          var id = $("#sub_category_id").val();
          var route = "{{route('stock-master.show','id')}}".replace('id', id);
          $.ajax({
               type: 'get',
               url: route,
               datatype: 'json',
               data: {
                    "id": id,
               },
               success: function(response) {
                    $("#product_id").empty('');
                    $.each(response, function(i, value) {
                         $("#product_id").append('<option value="' + value.id + '">' + value.product_name + ' : ' + value.quantity +'</option>');
                    });
               }
          });
     }
</script>
@endsection