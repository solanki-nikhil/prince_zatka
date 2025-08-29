@extends('layouts.app')
@section('title', 'Product')
@section('content')

<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">View Product</h2>
                        <a role="button" class="btn btn-primary float-right" href="{{route('product.create')}}">Add product</a>
                        <a role="button" class="btn btn-primary float-right filter mx-2">Filter</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="multiple-column-form">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Filter <small>Product</small></h3>
                            </div>
                            <div class="card-body">
                                <form id="product_report" class="form" action="javascript:void(0)" method="POST">
                                    @csrf
                                    <div class="row">
                                        <!--Start Category-->
                                        <div class="col-md-6 col-12 mb-1 custom-input-group">
                                            <label class="form-label" for="category_id">Category <span class="text-danger">*</span></label>
                                            <select id="category_id" name="category_id" class="form-select">
                                                <option selected>All</option>
                                                @foreach($category as $cat)
                                                <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!--End Category-->

                                        <!--Start Category-->
                                        <!--End Category-->
                                        <div class="col-md-12 col-12 custom-input-group text-end">
                                            <button type="button" class="btn btn-primary product-report" id="submit">Submit</button>
                                            <button type="submit" class="btn btn-primary product-report-export">Download</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="basic-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card p-2">
                            <table id="product" class="datatables-basic table">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Action</th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>ModelNo</th>
                                        <th>Per Box Pices</th>
                                        <th>Price</th>
                                        <th>Description</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- END: Content-->

<!-- Product Images Modal -->
<div class="modal fade" id="productImagesModal" tabindex="-1" aria-labelledby="productImagesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="productImagesModalLabel">Product Images</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex justify-content-center align-items-center flex-wrap gap-3" id="productImagesModalBody">
        <!-- Images will be injected here -->
      </div>
    </div>
  </div>
</div>
@endsection

@section('pagescript')
<script type="application/javascript">
    'use strict';
    const URL = "{{route('product.index')}}";

    //product listing using ajax server side datatable
    var table = '';
    $(function() {
        table = $('#product').DataTable({
            // ajax: URL,
            processing: true,
            serverSide: true,
            fixedHeader: true,
            scrollX: true,
            ajax: {
                url: URL,
                data: function(d) {
                    d.category_id = $('#category_id').val()
                    // ,d.sub_category_id = $('#sub_category_id').val()
                }
            },
            columns: [{
                    data: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    sortable: false
                },
                {
                    data: 'category.category_name',
                    name: 'category.category_name'
                },
                // {
                //     data: 'sub_category_name',
                //     name: 'subCategory.sub_category_name'
                // },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'product_code',
                    name: 'product_code'
                },
                // {
                //     data: 'quantity',
                //     name: 'quantity'
                // },
                {
                    data: 'box',
                    name: 'box'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'image',
                    name: 'image'
                },
            ],
            initComplete: function(settings, json) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            },

        });
    });


    $(document).on('click', '.delete', function() {
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
                    axios.delete(URL + '/' + id)
                        .then(function(response) {
                            if (response.data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Your record has been deleted.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                });
                                btn.parent().parent().parent().remove();
                            } else if (response.data.warning) {
                                toastr.warning(response.data.errorMessage, 'Opps!');
                            } else {
                                toastr.error(response.data.errorMessage, 'Opps!');
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

    $(document).ready(function() {
        $("#multiple-column-form").hide();
        $(document).on('click', '.filter', function() {
            if ($(this).text() == "Filter") {
                $(this).text("Close");
            } else {
                $(this).text("Filter");
            };
            $("#multiple-column-form").toggle();
        });
    });

    $(document).on('click', '.product-report', function() {
        table.draw();
    });

    // START :: For Excel Export 
    $(document).on('click', '.product-report-export', function() {
        $('#product_report').attr('action', "{{route('product-report-export')}}");
        $('form#product_report').submit();
    });
    // END :: For Excel Export 
</script>
<script>
$(document).on('click', '.product-image-modal-trigger', function(e) {
    e.preventDefault();
    const image = $(this).data('image');
    const image2 = $(this).data('image2');
    const image3 = $(this).data('image3');
    let html = '';
    if (image) {
        html += `<img src="${image}" class="img-fluid rounded m-2" style="max-width:250px;max-height:250px;">`;
    }
    if (image2) {
        html += `<img src="${image2}" class="img-fluid rounded m-2" style="max-width:250px;max-height:250px;">`;
    }
    if (image3) {
        html += `<img src="${image3}" class="img-fluid rounded m-2" style="max-width:250px;max-height:250px;">`;
    }
    if (!html) {
        html = '<p class="text-muted">No images available.</p>';
    }
    $('#productImagesModalBody').html(html);
    $('#productImagesModal').modal('show');
});
</script>
@endsection