@extends('layouts.app')
@section('title', 'Product')
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
                                        @if((isset($product) && isset($product->id)))
                                        <h3 class="card-title">Edit Product</h3>
                                        @else
                                        <h3 class="card-title">Add Product</h3>
                                        @endif
                                   </div>
                                   <div class="card-body">
                                        <form id="product_form" class="form" action="javascript:void(0);" method="POST">
                                             <div class="row">
                                                  @if((isset($product) && isset($product->id)))
                                                  <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                  @endif
                                                  <div class="col-md-4 col-12 mb-1">
                                                       <label for="image" class="form-label">Image</label>
                                                       <input class="form-control" type="file" id="image" name="image">
                                                       @if((isset($product) && isset($product->id)))
                                                       <img class="mt-1 rounded" src="{{asset('upload/product/'.$product->image)}}" height="100" width="100">
                                                       @endif
                                                  </div>
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="category_id">Category<span class="text-danger">*</span></label>
                                                       <select class="form-select" name="category_id" id="category_id">
                                                            <option value="" selected disabled>Select Category</option>
                                                            @foreach ($category as $item)
                                                            <option value="{{ $item->id }}" {{ (isset($product) && $product->category_id == $item->id ) ? 'selected' : '' }}>{{ $item->category_name}}</option>
                                                            @endforeach
                                                       </select>
                                                       <span class="invalid-feedback d-block" id="error_country_id" role="alert"></span>
                                                  </div>
                                                  <!-- <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="state_id">Sub Category<span class="text-danger">*</span></label>
                                                       <select class="form-select" name="sub_category_id" id="sub_category_id">
                                                            <option value="" selected disabled>Select Sub Category</option>
                                                            @if((isset($product) && isset($product->id)))
                                                                 @foreach ($subCategory as $item)
                                                                 <option value="{{ $item->id }}" {{ (isset($product) && $product->sub_category_id == $item->id ) ? 'selected' : '' }}>{{ $item->sub_category_name}}</option>
                                                                 @endforeach
                                                            @endif
                                                       </select>
                                                       <span class="invalid-feedback d-block" id="error_state_id" role="alert"></span>
                                                  </div> -->
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="product_name">Product<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="product_name" id="product_name" placeholder="Product Name" value="{{ ((isset($product) && isset($product->product_name)) ? $product->product_name : old('product_name'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_product_name" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="product_code">Model No.<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="product_code" id="product_code" placeholder="Model Number" value="{{ ((isset($product) && isset($product->product_code)) ? $product->product_code : old('product_code'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_product_code" role="alert"></span>
                                                  </div>
                                                  <!-- <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="quantity">Stock Quantity<span class="text-danger">*</span></label>
                                                       <input type="number" min="1" class="form-control" name="quantity" id="quantity" placeholder="Quantity" value="{{ ((isset($product) && isset($product->quantity)) ? $product->quantity : old('quantity'))  }}" @if((isset($product) && isset($product->id))) readonly @endif>
                                                       <span class="invalid-feedback d-block" id="error_quantity" role="alert"></span>
                                                  </div> -->
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="box">Per Box Pices<span class="text-danger">*</span></label>
                                                       <input type="number" min="1" class="form-control" name="box" id="box" placeholder="Box" value="{{ ((isset($product) && isset($product->box)) ? $product->box : old('box'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_box" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="price">Price<span class="text-danger">*</span></label>
                                                       <input type="number" min="1" class="form-control" name="price" id="price" placeholder="Price" value="{{ ((isset($product) && isset($product->price)) ? $product->price : old('price'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_price" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-4 col-12 mb-1">
                                                       <label class="form-label" for="description">Description</label>
                                                       <textarea class="form-control" name="description" id="description" rows="3" placeholder="Description">{{ ((isset($product) && isset($product->description)) ? $product->description : '')  }}</textarea>
                                                       <!-- <input type="text" class="form-control" name="description" id="description" placeholder="Description"> -->
                                                       <span class="invalid-feedback d-block" id="error_description" role="alert"></span>
                                                  </div>
                                                  
                                                  <div class="col-md-12 col-12 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right save-product">Submit</button>
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
          $('.save-product').on('click', function() {
               var formData = new FormData($("#product_form")[0]);
               if ($("#category_id").val() != "" && $("#product_name").val() != "" && $("#product_code").val() != "" && $("#box").val() != "" && $("#price").val() != "" && $("#box").val() >= 1 && $("#price").val() >= 1) {
                    $.ajax({
                         type: "POST",
                         url: "{{route('product.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $("#error_category_id").html(' ');
                              $("#error_sub_category_id").html(' ');
                              $("#error_product_name").html(' ');
                              $("#error_product_code").html(' ');
                              $("#error_quantity").html(' ');
                              $("#error_box").html(' ');
                              $("#error_price").html(' ');
                              $(".save-product").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait');
                              $(".save-product").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".save-product").html('Submit');
                              $(".save-product").attr('disabled', false);
                              if (response.status == false) {
                                   $.each(response.errors, function(key, value) {
                                        $('#error_' + key).html('<p class="text-danger mb-0">' + value + '</p>');
                                   });
                                   toastr.warning('Please Input Propper Data.', 'Warning');
                              } else if (response.server_error && response.status == true) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#product_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#product_form").validate({
                         rules: {
                              category_id: {
                                   required: true,
                              },
                              sub_category_id: {
                                   required: true,
                              },
                              product_name: {
                                   required: true,
                              },
                              product_code: {
                                   required: true,
                              },
                              quantity: {
                                   required: true,
                                   minlength:1
                              },
                              box: {
                                   required: true,
                                   minlength:1
                              },
                              price: {
                                   required: true,
                                   minlength:1
                              },
                         },
                         messages: {
                              category_id: {
                                   required: "Select Category"
                              },
                              sub_category_id: {
                                   required: "Select Sub Category"
                              },
                              product_name: {
                                   required: "Enter Product Name"
                              },
                              product_code: {
                                   required: "Enter Product Code"
                              },
                              quantity: {
                                   required: "Enter Quantity",
                                   minlength:"Please Input 1 or greater Number",
                              },
                              box: {
                                   required: "Enter Box",
                                   minlength:"Please Input 1 or greater Number",
                              },
                              price: {
                                   required: "Enter Price",
                                   minlength:"Please Input 1 or greater Number",
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
               changeCity();
          }
          // $("#category_id").on('change', function() {
          //      changeCity();
          // });
     });

     function changeCity() {
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
                    // $.each(response, function(i, value) {
                    //      $("#sub_category_id").append('<option value="' + value.id + '">' + value.sub_category_name + '</option>');
                    // });
               }
          });
     }
</script>
@endsection