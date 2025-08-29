@extends('layouts.app')
@section('title', 'Sub Category')
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
                                        @if((isset($subCategory) && isset($subCategory->id)))
                                        <h3 class="card-title">Edit <small>Sub Category</small></h3>
                                        @else
                                        <h3 class="card-title">Add <small>Sub Category</small></h3>
                                        @endif
                                   </div>
                                   <div class="card-body">
                                        <form id="sub_category_form" class="form" action="javascript:void(0);" method="POST" enctype="multipart/form-data">
                                             <div class="row">
                                                  @if((isset($subCategory) && isset($subCategory->id)))
                                                  <input type="hidden" name="sub_category_id" value="{{ $subCategory->id }}">
                                                  @endif
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="category_id">Category<span class="text-danger">*</span></label>
                                                       <select class="form-select" name="category_id" required id="category_id">
                                                            <option value="" selected disabled>Select Category</option>
                                                            @foreach ($category as $item)
                                                            <option value="{{ $item->id }}" {{ (isset($subCategory) && $subCategory->category_id == $item->id ) ? 'selected' : '' }}>{{ $item->category_name}}</option>
                                                            @endforeach
                                                       </select>
                                                       <span class="invalid-feedback d-block" id="error_category_id" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="sub_category_name">Sub Category<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="sub_category_name" id="sub_category_name" placeholder="Sub Category" value="{{ ((isset($subCategory) && isset($subCategory->sub_category_name)) ? $subCategory->sub_category_name : old('sub_category_name'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_sub_category_name" role="alert"></span>
                                                  </div>
                                                  <div class="col-lg-4 col-md-12 mb-1 mb-sm-0">
                                                       <label for="image" class="form-label">Image</label>
                                                       <input class="form-control" type="file" id="image" name="image">
                                                       @if((isset($subCategory) && isset($subCategory->id)))
                                                       <img class="mt-1 rounded" src="{{asset('upload/subcategory/'.$subCategory->image)}}" height="100" width="100">
                                                       @endif
                                                  </div>
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="distributor_discount">Distributor Discount<span class="text-danger">*</span></label>
                                                       <input type="number" min="1" max="100" class="form-control" name="distributor_discount" id="distributor_discount" placeholder="Distributor Discount" value="{{ ((isset($subCategory) && isset($subCategory->distributor_discount)) ? $subCategory->distributor_discount : old('distributor_discount'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_distributor_discount" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="dealer_discount">Dealer Discount<span class="text-danger">*</span></label>
                                                       <input type="number" min="1" max="100" class="form-control" name="dealer_discount" id="dealer_discount" placeholder="Dealer Discount" value="{{ ((isset($subCategory) && isset($subCategory->dealer_discount)) ? $subCategory->dealer_discount : old('dealer_discount'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_dealer_discount" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-12 col-12 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right save-sub-category">Submit</button>
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
          $('.save-sub-category').on('click', function() {
               var formData = new FormData($("#sub_category_form")[0]);
               if ($("#category_id").val() != "" && $("#sub_category_name").val() != "" && $("#distributor_discount").val() != "" && $("#dealer_discount").val() != "") {
                    $.ajax({
                         type: "POST",
                         url: "{{route('sub-category.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $("#error_sub_category_name").html(' ');
                              $("#error_category_id").html(' ');                              
                              $(".save-sub-category").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait');
                              $(".save-sub-category").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".save-sub-category").html('Submit');
                              $(".save-sub-category").attr('disabled', false);
                              if (response.status == false) {
                                   $.each(response.errors, function(key, value) {
                                        $('#error_' + key).html('<p class="text-danger mb-0">' + value + '</p>');
                                   });
                                   toastr.warning('Please Input Propper Data.', 'Warning');
                              } else if (response.server_error && response.status == true) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#sub_category_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#sub_category_form").validate({
                         rules: {
                              category_id: {
                                   required: true,
                              },
                              sub_category_name: {
                                   required: true,
                              },
                              distributor_discount: {
                                   required: true,
                              },
                              dealer_discount: {
                                   required: true,
                              },
                         },
                         messages: {
                              category_id: {
                                   required: "Select Category"
                              },
                              sub_category_name: {
                                   required: "Enter Sub Category"
                              },
                              distributor_discount: {
                                   required: "Enter Distributor Discount"
                              },
                              dealer_discount: {
                                   required: "Enter Dealer Discount"
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
</script>
@endsection