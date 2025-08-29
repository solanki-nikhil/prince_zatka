@extends('layouts.app')
@section('title', 'Add User')
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
                                        @if((isset($userProfile) && isset($userProfile->id)))
                                        <h3 class="card-title">Edit <small>User</small></h3>
                                        @else
                                        <h3 class="card-title">Add <small>User</small></h3>
                                        @endif
                                   </div>
                                   <div class="card-body">
                                        <form id="user_form" class="form" action="javascript:void(0);" method="POST">
                                             <div class="row">
                                                  @if((isset($userProfile) && isset($userProfile->id)))
                                                  <input type="hidden" name="user_id" value="{{ $userProfile->user->id }}">
                                                  @endif

                                                  <div class="col-12 col-md-4 mb-1 custom-input-group">
                                                       <label class="form-label" for="name">Name<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="{{ ((isset($userProfile) && isset($userProfile->user->name)) ? $userProfile->user->name : old('name '))  }}">
                                                       <span class="invalid-feedback d-block" id="error_name" role="alert"></span>
                                                  </div>
                                                  <!-- <div class="col-12 col-md-4 col-lg-3 mb-1 custom-input-group">
                                                       <label class="form-label" for="email">Email<span class="text-danger"></span></label>
                                                       <input type="text" class="form-control" name="email" id="email" placeholder="Enter Email" value="{{ ((isset($userProfile) && isset($userProfile->user->email)) ? $userProfile->user->email : old('email'))  }}" @if(isset($userProfile) && $userProfile->id) readonly @endif>
                                                       <span class="invalid-feedback d-block" id="error_email" role="alert"></span>
                                                  </div> -->
                                                  <div class="col-12 col-md-4 mb-1 custom-input-group">
                                                       <label class="form-label" for="mobile">Mobile No.<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Enter Mobile No." value="{{ ((isset($userProfile) && isset($userProfile->user->mobile)) ? $userProfile->user->mobile : old('mobile'))  }}" @if(isset($userProfile) && $userProfile->id) readonly @endif>
                                                       <span class="invalid-feedback d-block" id="error_mobile" role="alert"></span>
                                                  </div>
                                                  <div class="col-12 col-md-4 mb-1 custom-input-group">
                                                       <label class="form-label" for="password">Password @if(isset($userProfile) && $userProfile->id) @else <span class="text-danger">*</span> @endif</label>
                                                       <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password" value="">
                                                       <span class="invalid-feedback d-block" id="error_password" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="country_id">Area<span class="text-danger">*</span></label>
                                                       <select class="form-select" name="country_id" id="country_id">
                                                            <option value="" selected disabled>Select Area</option>
                                                            @foreach ($country as $item)
                                                            <option value="{{ $item->id }}" {{ (isset($userProfile) && $userProfile->country_id == $item->id ) ? 'selected' : '' }}>{{ $item->country_name}}</option>
                                                            @endforeach
                                                       </select>
                                                       <span class="invalid-feedback d-block" id="error_country_id" role="alert"></span>
                                                  </div>
                                                  <div class="col-12 col-md-4 mb-1 custom-input-group">
                                                       <label class="form-label" for="name">Company Name<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Enter Commpany Name" value="{{ ((isset($userProfile) && isset($userProfile->user->company_name)) ? $userProfile->user->company_name : old('company_name'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_name" role="alert"></span>
                                                  </div>
                                                  <div class="col-12 col-md-4 mb-1 custom-input-group">
                                                       <label class="form-label" for="name">GST Number<span class="text-danger"></span></label>
                                                       <input type="text" class="form-control" name="gst" id="gst" placeholder="Enter GST" value="{{ ((isset($userProfile) && isset($userProfile->user->gst)) ? $userProfile->user->gst : old('gst'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_name" role="alert"></span>
                                                  </div>


                                                  <div class="col-12 mb-1">
                                                       <label class="form-label" for="address">Address</label>
                                                       <textarea class="form-control" name="address" id="address" rows="3" placeholder="type address..">{{ ((isset($userProfile) && isset($userProfile->address)) ? $userProfile->address : '')  }}</textarea>
                                                       <span class="invalid-feedback d-block" id="error_address" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-12 col-12 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right save-user">Submit</button>
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
          $('.save-user').on('click', function() {
               var formData = new FormData($("#user_form")[0]);
               if ($("#country_id").val() != "" && $("#name").val() != "" && $("#mobile").val() != "") {
                    $.ajax({
                         type: "POST",
                         url: "{{route('user.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $(".save-user").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait');
                              $(".save-user").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".save-user").html('Submit');
                              $(".save-user").attr('disabled', false);
                              if (response.status == false) {
                                   $.each(response.errors, function(key, value) {
                                        $('#error_' + key).html('<p class="text-danger mb-0">' + value + '</p>');
                                   });
                                   toastr.warning('Please Input Propper Data.', 'Warning');
                              } else if (response.server_error && response.status == true) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#user_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#user_form").validate({
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
                                   minlength: 1
                              },
                              box: {
                                   required: true,
                                   minlength: 1
                              },
                              price: {
                                   required: true,
                                   minlength: 1
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
                                   minlength: "Please Input 1 or greater Number",
                              },
                              box: {
                                   required: "Enter Box",
                                   minlength: "Please Input 1 or greater Number",
                              },
                              price: {
                                   required: "Enter Price",
                                   minlength: "Please Input 1 or greater Number",
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
          // var city_id = $("#city_id").val();
          // if (city_id != "") {
          //      changeCountry();
          // }
          // $("#country_id").on('change', function() {
          //      changeCountry();
          // });

          // $("#state_id").on('change', function() {
          //      changeState();
          // });
     });

     function changeCountry() {
          var id = $("#country_id").val();
          var route = "{{route('city.show','id')}}".replace('id', id);
          $.ajax({
               type: 'get',
               url: route,
               datatype: 'json',
               data: {
                    "id": id,
               },
               success: function(response) {
                    $("#state_id").empty('');
                    $("#state_id").append('<option value="" selected disabled>Select State</option>');
                    $.each(response, function(i, value) {
                         $("#state_id").append('<option value="' + value.id + '">' + value.state_name + '</option>');
                    });
               }
          });
     }

     function changeState() {
          var state_id = $("#state_id").val();
          var route = "{{route('city.update','state_id')}}".replace('state_id', state_id);
          $.ajax({
               type: 'PUT',
               url: route,
               datatype: 'json',
               data: {
                    "state_id": state_id,
               },
               success: function(response) {
                    $("#city_id").empty('');
                    $.each(response, function(i, value) {
                         $("#city_id").append('<option value="' + value.id + '">' + value.city_name + '</option>');
                    });
               }
          });
     }
</script>
@endsection