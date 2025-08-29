@extends('layouts.app')
@section('title', 'Category')
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
                                        @if((isset($category) && isset($category->id)))
                                        <h3 class="card-title">Edit <small>Category</small></h3>
                                        @else
                                        <h3 class="card-title">Add <small>Category</small></h3>
                                        @endif
                                   </div>
                                   <div class="card-body">
                                        <form id="category_form" class="form" action="javascript:void(0);" method="POST" enctype="multipart/form-data">
                                             <div class="row">
                                                  @if((isset($category) && isset($category->id)))
                                                  <input type="hidden" name="category_id" value="{{ $category->id }}">
                                                  @endif
                                                  <div class="col-md-6 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="category_name">Category Name<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="category_name" id="category_name" placeholder="Category Name" value="{{ ((isset($category) && isset($category->category_name)) ? $category->category_name : old('category_name'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_category_name" role="alert"></span>
                                                  </div>
                                                  <div class="col-lg-6 col-md-12 mb-1 mb-sm-0">
                                                       <label for="image" class="form-label">Image</label>
                                                       <input class="form-control" type="file" id="image" name="image">
                                                       @if((isset($category) && isset($category->id)))
                                                       <img class="mt-1 rounded" src="{{asset('upload/category/'.$category->image)}}" height="100" width="100">
                                                       @endif
                                                  </div>
                                                  <div class="col-md-12 col-12 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right save-category">Submit</button>
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
          $('.save-category').on('click', function() {
               var formData = new FormData($("#category_form")[0]);
               if ($("#category_name").val() != "") {
                    $.ajax({
                         type: "POST",
                         url: "{{route('category.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $("#error_category_name").html(' ');
                              $(".save-category").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait');
                              $(".save-category").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".save-category").html('Submit');
                              $(".save-category").attr('disabled', false);
                              if (response.status == false) {
                                   $.each(response.errors, function(key, value) {
                                        $('#error_' + key).html('<p class="text-danger mb-0">' + value + '</p>');
                                   });
                                   toastr.warning('Please Input Propper Data.', 'Warning');
                              } else if (response.server_error && response.status == true) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#category_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#category_form").validate({
                         rules: {
                              category_name: {
                                   required: true,
                              },
                         },
                         messages: {
                              category_name: {
                                   required: "Enter Category"
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