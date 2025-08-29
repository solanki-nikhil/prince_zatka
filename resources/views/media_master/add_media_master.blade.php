@extends('layouts.app')
@section('title', 'Media Master')
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
                                        @if((isset($mediaMaster) && isset($mediaMaster->id)))
                                        <h3 class="card-title">Edit <small>Media</small></h3>
                                        @else
                                        <h3 class="card-title">Add <small>Media</small></h3>
                                        @endif
                                   </div>
                                   <div class="card-body">
                                        <form id="media_master_form" class="form" action="javascript:void(0);" method="POST" enctype="multipart/form-data">
                                             <div class="row">
                                                  @if((isset($mediaMaster) && isset($mediaMaster->id)))
                                                  <input type="hidden" name="media_id" value="{{ $mediaMaster->id }}">
                                                  @endif
                                                  <div class="col-md-6 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="title">Title<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="title" id="title" placeholder="Title" value="{{ ((isset($mediaMaster) && isset($mediaMaster->title)) ? $mediaMaster->title : old('title')) }}">
                                                       <span class="invalid-feedback d-block" id="error_title" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-6 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="description">Description</label>
                                                       <input type="text" class="form-control" name="description" id="description" placeholder="Description" value="{{ ((isset($mediaMaster) && isset($mediaMaster->description)) ? $mediaMaster->description : old('description'))  }}">
                                                  </div>
                                                  <div class="col-md-6 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="link">Video Link</label>
                                                       <input type="text" class="form-control" name="link" id="link" placeholder="Video Link" value="{{ ((isset($mediaMaster) && isset($mediaMaster->link)) ? $mediaMaster->link : old('link'))  }}">
                                                  </div>
                                                  <div class="col-lg-6 col-md-12 mb-1 mb-sm-0">
                                                       <label for="image" class="form-label">Image</label>
                                                       <input class="form-control" type="file" id="image" name="image">
                                                       @if((isset($mediaMaster) && isset($mediaMaster->id)))
                                                       <img class="mt-1 rounded" src="{{asset('upload/media/'.$mediaMaster->image)}}" height="100" width="100">
                                                       @endif
                                                  </div>
                                                  <div class="col-md-12 col-12 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right save-media-master">Submit</button>
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
          $('.save-media-master').on('click', function() {
               var formData = new FormData($("#media_master_form")[0]);
               if ($("#title").val() != "") {
                    $.ajax({
                         type: "POST",
                         url: "{{route('media-master.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $("#error_title").html(' ');
                              $(".save-media-master").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait');
                              $(".save-media-master").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".save-media-master").html('Submit');
                              $(".save-media-master").attr('disabled', false);
                              if (response.status == false) {
                                   $.each(response.errors, function(key, value) {
                                        $('#error_' + key).html('<p class="text-danger mb-0">' + value + '</p>');
                                   });
                                   toastr.warning('Please Input Propper Data.', 'Warning');
                              } else if (response.server_error && response.status == true) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#media_master_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#media_master_form").validate({
                         rules: {
                              title: {
                                   required: true,
                              },
                         },
                         messages: {
                              title: {
                                   required: "Enter Title"
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