@extends('layouts.app')
@section('title', 'Add Order')
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
                                        @if((isset($order) && isset($order->id)))
                                        <div style="width: 100%;">
                                             <div class="d-flex justify-content-between">
                                                  <h3 class="card-title">Edit Orderss</h3>
                                                  <div>
                                                       <button class="btn btn-icon btn-danger delete-order" type="button" data-id="{{$order->id}}">
                                                            <i class="fas fa-trash-alt me-25"></i>
                                                            <span>Delete Order</span>
                                                       </button>
                                                  </div>
                                             </div>
                                        </div>
                                        @else
                                        <h3 class="card-title">Add Order</h3>
                                        @endif
                                   </div>
                                   <div class="card-body">
                                        <form id="order_form" class="form invoice-repeater" action="javascript:void(0);" method="POST">
                                             <div class="row">


                                                  <div class="col-12 col-md-4 col-lg-3 mb-1 custom-input-group" id="self_name">
                                                       <label class="form-label" for="user_id">User<span class="text-danger">*</span></label>
                                                       @if(Auth::user()->roles[0]->name == 'customer')
                                                       <select class="form-select" name="user_id" id="user_id" disabled>
                                                            <option value="" selected disabled>Select Customer</option>
                                                            @foreach ($user as $item)
                                                            <option value="{{ $item->id }}" {{ (isset($order) && $order->user_id == $item->id ) ? 'selected' : '' }}>{{ $item->name }}</option>
                                                            @endforeach
                                                       </select>
                                                       @else
                                                       <select class="form-select" name="user_id" id="user_id">
                                                            <option value="" selected disabled>Select Customer</option>
                                                            @foreach ($user as $item)
                                                            <option value="{{ $item->id }}" {{ (isset($order) && $order->user_id == $item->id ) ? 'selected' : '' }}>{{ $item->name }}</option>
                                                            @endforeach
                                                       </select>
                                                       @endif
                                                       <span class="invalid-feedback d-block" id="error_user_id" role="alert"></span>
                                                  </div>


                                                  <div class="col-12 col-md-4 col-lg-3 mb-1 custom-input-group">
                                                       <label class="form-label" for="mobile">Mobile No.<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Enter Mobile No." value="{{ ((isset($order) && isset($order->mobile)) ? $order->mobile : old('mobile'))  }}" disabled>
                                                       <span class="invalid-feedback d-block" id="error_mobile" role="alert"></span>
                                                  </div>

                                                  <div class="col-12 col-md-4 col-lg-6 mb-1 custom-input-group">
                                                       <label class="form-label" for="mobile">Address</label>
                                                       <input type="text" class="form-control" name="address" id="address" value="{{ ((isset($order) && isset($order->address)) ? $order->address : '')  }}" disabled>
                                                       <span class="invalid-feedback d-block" id="error_mobile" role="alert"></span>
                                                  </div>

                                                  <hr>
                                                  @if((isset($order) && isset($order->id)))
                                                  <input type="hidden" name="order_id" id="order_id" value="{{ $order->id }}">

                                                  <div data-repeater-list="invoice">
                                                       @foreach($order->orderTransection as $key => $value)
                                                       <div data-repeater-item>
                                                            <div class="row">
                                                                 <div class="col-10">
                                                                      <div class="row">
                                                                           <div class="col-12 col-md-6 col-lg-1 mb-1">
                                                                                <label class="form-label">SrNo</label>
                                                                                <input type="text" min="1" class="form-control sr_no w-75 text-center" name="sr_no" placeholder="Sr.No." value="{{$key+1}}" disabled>
                                                                           </div>
                                                                           <input type="hidden" name="order_transection_id" id="order_transection_id" value="{{ $value->id }}">
                                                                           <div class="col-12 col-md-6 col-lg-2 mb-1 custom-input-group">
                                                                                <label class="form-label">Category<span class="text-danger">*</span></label>
                                                                                <select class="form-select category_select" name="category_id" data-key="0">
                                                                                     <option value="" selected disabled>--Select--</option>
                                                                                     @foreach ($category as $item)
                                                                                     <option value="{{ $item->id }}" {{ ($value->category_id == $item->id ) ? 'selected' : '' }}>{{ $item->category_name}}</option>
                                                                                     @endforeach
                                                                                </select>
                                                                           </div>
                                                                           <div class="col-12 col-md-6 col-lg-4 mb-2 custom-input-group">
                                                                                <label class="form-label">Product<span class="text-danger">*</span></label>
                                                                                <select class="select2 form-select form-select-lg product_select" name="product_id">
                                                                                     <option value="" selected disabled>--Select--</option>
                                                                                     @foreach ($value->product as $item)
                                                                                     <option value="{{ $item->id }}" {{ ($value->product_id == $item->id ) ? 'selected' : '' }}>{{ $item->product_name}} / {{$item->product_code}}</option>
                                                                                     @endforeach
                                                                                </select>
                                                                           </div>
                                                                           <div class="col-12 col-md-6 col-lg-2 mb-1 custom-input-group">
                                                                                <label class="form-label">Box/Bag<span class="text-danger">*</span></label>
                                                                                <input type="number" min="1" class="form-control quantity" name="quantity" placeholder="Quantity" value="{{ $value->box }}">
                                                                           </div>
                                                                           <div class="col-12 col-md-6 col-lg-1 mb-1 custom-input-group">
                                                                                <label class="form-label">Box Pc</label>
                                                                                <input type="text" min="1" class="form-control box" name="box" placeholder="Box" value="{{ $value->per_box_pices }}" disabled>
                                                                           </div>
                                                                           <div class="col-12 col-md-6 col-lg-2 mb-1 custom-input-group">
                                                                                <label class="form-label">Price</label>
                                                                                <input type="hidden" min="1" class="form-control price" name="price" placeholder="Price" value="{{ $value->amount }}">
                                                                                <input type="text" min="1" class="form-control price_total" name="price_total" placeholder="Price Total" value="{{ $value->amount }}" disabled>
                                                                           </div>

                                                                      </div>
                                                                 </div>
                                                                 <div class="col-2">
                                                                      <div class="row">
                                                                           <div class="col-12 col-md-6 col-lg-8 mb-1 custom-input-group">
                                                                                <label class="form-label" for="total">Total</label>
                                                                                <input type="number" class="form-control total" name="total" id="total" placeholder="Total" value="{{ $value->total_amount }}" disabled>
                                                                           </div>
                                                                           <div class="col-12 col-md-6 col-lg-4 pt-2">
                                                                                <button class="btn float-right btn-outline-danger text-nowrap px-1 remove-item data-repeater-delete" data-id="{{$value->id}}" data-repeater-delete type="button">
                                                                                     <i data-feather="x" class="me-25"></i>
                                                                                </button>
                                                                           </div>
                                                                      </div>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                       @endforeach
                                                  </div>
                                                  <hr>
                                                  <div class="col-12 col-md-6">
                                                       <button class="btn btn-icon btn-primary" type="button" data-repeater-create>
                                                            <i data-feather="plus" class="me-25"></i>
                                                            <span>Add New</span>
                                                       </button>
                                                  </div>
                                                  @else
                                                  <div data-repeater-list="invoice">
                                                       <div data-repeater-item>
                                                            <div class="row">
                                                                 <div class="col-10">
                                                                      <div class="row">
                                                                           <div class="col-12 col-md-6 col-lg-1 mb-1">
                                                                                <label class="form-label">SrNo</label>
                                                                                <input type="text" min="1" class="form-control sr_no w-75 text-center" name="sr_no" value="1" disabled>
                                                                           </div>
                                                                           <div class="col-12 col-md-6 col-lg-2 mb-1 custom-input-group">
                                                                                <label class="form-label">Category<span class="text-danger">*</span></label>
                                                                                <select class="form-select category_select" name="category_id" id="category_id" data-key="0">
                                                                                     <option value="" selected disabled>--Select--</option>
                                                                                     @foreach ($category as $item)
                                                                                     <option value="{{ $item->id }}" {{ (isset($order) && $order->category_id == $item->id ) ? 'selected' : '' }}>{{ $item->category_name}}</option>
                                                                                     @endforeach
                                                                                </select>
                                                                           </div>

                                                                           <div class="col-12 col-md-6 col-lg-4 mb-1 custom-input-group">
                                                                                <label class="form-label">Product<span class="text-danger">*</span></label>
                                                                                <select name="product_id" id="product_id" class="select2 form-select form-select-lg product_select" data-allow-clear="true">
                                                                                     <option value="" selected disabled>--Select--</option>
                                                                                </select>
                                                                           </div>
                                                                           <div class="col-12 col-md-6 col-lg-2 mb-1 custom-input-group">
                                                                                <label class="form-label">Box/Bag<span class="text-danger">*</span></label>
                                                                                <input type="number" min="1" class="form-control quantity" name="quantity" id="quantity" placeholder="Quantity" value="{{ old('quantity') }}">
                                                                           </div>
                                                                           <div class="col-12 col-md-6 col-lg-1 mb-1 custom-input-group ">
                                                                                <label class="form-label">Box Pc</label>
                                                                                <input type="text" min="1" class="form-control box" name="box" id="box" placeholder="Box" value="{{ old('box') }}" disabled>
                                                                           </div>
                                                                           <div class="col-12 col-md-6 col-lg-2 mb-1 custom-input-group">
                                                                                <label class="form-label">Price </label>
                                                                                <input type="hidden" min="1" class="form-control price" name="price" id="price" placeholder="Price" value="{{ old('price') }}">
                                                                                <input type="text" min="1" class="form-control price_total" name="price_total" id="price_total" placeholder="Price" value="{{ old('price') }}" disabled>

                                                                           </div>

                                                                      </div>
                                                                 </div>
                                                                 <div class="col-2">
                                                                      <div class="row">
                                                                           <div class="col-12 col-md-6 col-lg-8 mb-1 custom-input-group">
                                                                                <label class="form-label" for="total">Total</label>
                                                                                <input type="number" class="form-control total" name="total" placeholder="Total" value="" disabled>

                                                                           </div>
                                                                           <div class="col-12 col-md-6 col-lg-4 pt-2">
                                                                                <button class="btn btn-outline-danger text-nowrap px-1  data-repeater-delete remove-item" data-repeater-delete type="button">
                                                                                     <i data-feather="x" class="me-25"></i>
                                                                                </button>
                                                                           </div>
                                                                      </div>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                                  <hr>
                                                  <div class="col-12 col-md-6">
                                                       <button class="btn btn-icon btn-primary" type="button" data-repeater-create>
                                                            <i data-feather="plus" class="me-25"></i>
                                                            <span>Add New</span>
                                                       </button>
                                                  </div>
                                                  @endif
                                                  <div class="col-12 col-md-6 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right save-order">
                                                            <?php echo isset($order->id) ? 'Update' : 'Submit'; ?>
                                                       </button>
                                                       <button class="btn bg-light float-right mx-1 grand-total">
                                                            <?php echo isset($order->id) ? $order->total_amount : 'Grand Total'; ?>
                                                       </button>
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

     function changeCategory(id, me) {
          console.log(id); // Check the value of id in the console

          var route = "{{route('product.productFromCat', 'id')}}".replace('id', id); // Dynamic route for category ID
          $.ajax({
               type: 'get',
               url: route,
               datatype: 'json',
               success: function(response) {
                    console.log('Response from server:', response);

                    // Check if 'response.product' exists and has products
                    if (response.product && response.product.length > 0) {
                         me.parent().parent().find('.product_select').empty().trigger('change');
                         me.parent().parent().find('.product_select').select2("val", "");
                         me.parent().parent().find('.product_select').append('<option value="" selected>--Select--</option>');

                         $.each(response.product, function(i, value) {
                              me.parent().parent().find('.product_select').append('<option value="' + value.id + '">' + value.product_name + ' / ' + value.product_code + '</option>');
                         });
                    } else {
                         // If no products, display a warning
                         toastr.warning('No products found for this category', 'Warning');
                    }
               }

          });

     }

     function changeCategoryOld(id, me) {
          var route = "{{route('product.show','id')}}".replace('id', id);
          $.ajax({
               type: 'get',
               url: route,
               datatype: 'json',
               data: {
                    "id": id,
                    "user_id": user_id,
               },
               success: function(response) {
                    me.parent().parent().find('.sub_category_select').empty();
                    me.parent().parent().find('.sub_category_select').append('<option value="" selected disabled>--Select--</option>');
                    $.each(response, function(i, value) {
                         me.parent().parent().find('.sub_category_select').append('<option value="' + value.id + '">' + value.sub_category_name + '</option>');
                    });
               }
          });
     }


     function changeProduct(product_id, me) {
          var route = "{{route('product-change')}}";
          $.ajax({
               type: 'POST',
               url: route,
               datatype: 'json',
               data: {
                    "product_id": product_id,
                    "_token": "{{ csrf_token() }}"
               },
               success: function(response) {
                    me.parent().parent().parent().find('.box').val(response.box);
                    me.parent().parent().parent().find('.price').val(response.price);
                    me.parent().parent().parent().find('.price_total').val(response.price);
                    get_total(me);
               }
          });
     }

     $(function() {
          'use strict';
          // form repeater jquery
          $('.invoice-repeater, .repeater-default').repeater({
               show: function() {
                    $(this).slideDown();
                    var obj = $(this);
                    // Feather Icons
                    if (feather) {
                         feather.replace({
                              width: 14,
                              height: 14
                         });
                    }
                    // selectTwo();
                    $('.product_select').select2({
                         placeholder: "--Select--",
                         allowClear: false
                    });
                    // $(this).find('.select2-container').remove();
                    // $(this).find('.product_select').select2({
                    //    width: '100%',
                    //     placeholder: "Placeholder text",
                    //     allowClear: true
                    // });
                    var sr = $('.sr_no').length;
                    $(this).find('.sr_no').val(sr);
                    $(document).on("change", ".category_select", function() {
                         var user_id = $("#user_id").val();
                         if (user_id != "" && user_id != null) {
                              var id = $(this).val();
                              var me = $(this);
                              changeCategory(id, me);
                         } else {
                              toastr.warning('Select User.', 'Warning');
                         }
                    });


                    $(document).on("change", ".product_select", function() {
                         var me = $(this);
                         var id = this.value;
                         changeProduct(id, me);
                    });
                    $(document).on("keyup", ".quantity", function() {
                         var id = $(this).val();
                         var me = $(this);
                         get_total(me);
                    });

                    $("#user_id").on('change', function() {

                    });

                    var order_id = $("#order_id").val();


               },
               hide: function(deleteElement) {
                    if (($('.remove-item').length) > 1) {
                         $(this).slideUp(deleteElement);
                    } else {
                         Swal.fire({
                              text: "Cannot delete first item",
                              icon: 'warning',
                              confirmButtonText: 'OK',
                         });
                    }
               }
          });
     });

     $(document).ready(function() {
          $('.product_select').select2({
               placeholder: "--Select--",
               allowClear: false
          });
          $('.save-order').on('click', function() {
               var formData = new FormData($("#order_form")[0]);
               var temp = $("#user_id").val() != "" || $("#name").val() != "";
               //if ($("#category_id").val() != "" && $("#sub_category_id").val() != "" && $("#product_id").val() != "" && $("#quantity").val() != "") {
               if (temp && $("#mobile").val() != "") {
                    $.ajax({
                         type: "POST",
                         url: "{{route('order.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $(".save-order").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait');
                              $(".save-order").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".save-order").html('Submit');
                              $(".save-order").attr('disabled', false);
                              if (response.status == false) {
                                   toastr.warning(response.message, 'Warning');
                              } else if (response.server_error && response.status == false) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#order_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#order_form").validate({
                         rules: {
                              user_id: {
                                   required: true,
                              },
                              // sub_category_id: {
                              //      required: true,
                              // },
                              product_id: {
                                   required: true,
                              },
                              name: {
                                   required: true,
                              },
                              mobile: {
                                   required: true,
                              },
                              quantity: {
                                   required: true,
                                   minlength: 1
                              }
                         },
                         messages: {
                              user_id: {
                                   required: "Select User"
                              },
                              // sub_category_id: {
                              //      required: "Select Sub Category"
                              // },
                              product_id: {
                                   required: "Select Product"
                              },
                              name: {
                                   required: "Enter Name"
                              },
                              mobile: {
                                   required: "Enter Mobile No."
                              },
                              quantity: {
                                   required: "Enter Quantity",
                                   minlength: "Please Input 1 or greater Number",
                              }
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

     $(document).on('click', '#self', function() {
          $('#other_name').addClass('d-none');
          $("#name").val("");
          $("#mobile").val("");
          $('#address').val('').empty();
          changeUser();
     });

     $(document).on('click', '#other', function() {
          $('#other_name').removeClass('d-none');
          $('#mobile').val("");
          $('#address').val('').empty();
     });


     function changePro() {
          $(".product_select").each(function() {
               var me = $(this);
               var id = me.val();
               changeProduct(id, me);
          });
     }


     $(document).ready(function() {
          $(document).on("change", ".category_select", function() {
               var user_id = $("#user_id").val();
               if (user_id != "" && user_id != null) {
                    var id = $(this).val();
                    var me = $(this);
                    changeCategory(id, me);
               } else {
                    toastr.warning('Select User.', 'Warning');
               }
          });


          $(document).on("change", ".product_select", function() {
               var id = $(".product_select option:selected").val();
               var me = $(this);
               changeProduct(id, me);
          });

          $(document).on("keyup", ".quantity", function() {
               var id = $(this).val();
               var me = $(this);
               get_total(me);
          });

          $("#user_id").on('change', function() {
               changeUser();
          });

          var order_id = $("#order_id").val();

     });

     function changeUser() {
          var user_id = $("#user_id").val();
          var route = "{{route('user-change')}}";
          $.ajax({
               type: 'POST',
               url: route,
               datatype: 'json',
               data: {
                    "user_id": user_id,
                    "_token": "{{ csrf_token() }}"
               },
               success: function(response) {
                    $("#mobile").val(response.user.mobile);
                    $("#address").val(response.address);
               }
          });
     }

     function get_total(me) {

          var price = me.parent().parent().parent().find('.price').val();
          var qty = me.parent().parent().parent().find('.quantity').val();
          var total = eval(qty * price);

          me.parent().parent().parent().next().find('.total').val(total);
          var count = $('.total').length;
          get_gtotal(total);

     }

     function get_gtotal(total) {
          var sum = 0;
          $(".total").each(function() {
               // sum += parseInt(total);
               sum += parseInt($(this).val());
          });
          $('.grand-total').text(sum);
     }


     //delete product using ajax
     $(document).on('click', '.remove-item', function() {
          if (($('.remove-item').length) > 1) {
               var btn = $(this);
               var id = btn.data('id');
               if (id != null) {
                    $.ajax({
                         url: "{{route('edit-single-order-delete')}}",
                         type: "POST",
                         dataType: 'JSON',
                         data: {
                              "_token": "{{ csrf_token() }}",
                              "id": id,
                         },
                         success: function(response) {
                              $(this).slideDown();
                         }
                    });
               }
          } else {
               Swal.fire({
                    text: "Cannot delete first item",
                    icon: 'warning',
                    confirmButtonText: 'OK',
               });
          }
     });

     // delete order
     const apiURL = "{{route('order.index')}}";
     $(document).on('click', '.delete-order', function() {
          
          var btn = $(this);
          var id = btn.data('id');
          Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                         confirmButton: 'btn btn-primary',
                         cancelButton: 'btn btn-outline-danger ms-1'
                    },
                    buttonsStyling: false
               })
               .then(function(result) {
                    if (result.value) {
                         axios.delete(apiURL + '/' + id)
                              .then(function(response) {
                                   if (response.data.status == true) {
                                        Swal.fire({
                                             icon: 'success',
                                             title: 'Deleted!',
                                             text: 'Your record has been deleted.',
                                             customClass: {
                                                  confirmButton: 'btn btn-success'
                                             },
                                             timer: 2000,
                                        }).then((result) => {
                                             // Go back on timer end OR confirm button click
                                             if (result.dismiss === Swal.DismissReason.timer || result.isConfirmed) {
                                                  // window.history.back();
                                                  window.location.href = document.referrer;
                                             }
                                        });
                                        // btn.parent().parent().parent().remove();
                                   } else if (response.data.status == false) {
                                        toastr.warning(response.data.message, 'Opps!');
                                   } else {
                                        toastr.error(response.data.message, 'Opps!');
                                   }
                              })
                              .catch(function() {
                                   toastr.error('Someting went wrong. Please try again.', 'Opps!');
                              });
                    } else {
                         Swal.fire({
                              text: 'Your Order is safe!'
                         });
                    }
               });
     });
</script>
@endsection