@extends('layouts.app')
@section('title', 'Add/Check Warranty')
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl d-flex justify-content-center align-items-center">
        <div class="content-body w-75">
            <section id="multiple-column-form">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-10 col-12">
                        <div class="card">
                            <div class="card-header text-center">
                                <h3 class="card-title">Add/Check Warranty</h3>
                            </div>
                            <div class="card-body">
                                <form id="warranty_form" class="form" method="POST">
                                    @csrf
                                    <div class="row justify-content-center">
                                        <!-- Serial Number (First Field) -->
                                        <div class="col-8 mb-1">
                                            <input type="hidden" id="serial_no_id" name="serial_no_id">
                                            <label class="form-label" for="serial_number">Serial Number<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="serial_number" id="serial_number" placeholder="Enter Serial Number" required>
                                        </div>

                                        <!-- Check Button -->
                                        <div class="col-12 text-center mt-1">
                                            <button type="button" class="btn btn-secondary check-warranty">Check</button>
                                            <button type="button" class="btn btn-primary replace-item" style="display: none;">Replace Item</button>
                                        </div>

                                        <div id="replace_item_section" class="row mt-1" style="display: none;">
                                            <div class="col-12">
                                                <div class="p-2 border rounded bg-light position-relative"> <!-- Border & Background -->
                                                    <!-- Close Button -->
                                                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" id="close_replace_item" aria-label="Close"></button>

                                                    <div class="row justify-content-center">
                                                        <!-- Serial Number Input -->
                                                        <div class="col-md-8 mb-0">
                                                            <label class="form-label" for="new_serial_number">New Serial Number <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="new_serial_number" id="new_serial_number" placeholder="Enter New Serial Number">
                                                                <button type="button" class="btn btn-primary save-warranty-new">Save</button>
                                                            </div>
                                                        </div>
                                                        <!-- place new order -->
                                                        @if(Auth::user()->roles[0]->name == 'customer')
                                                        <div class="col-md-8 mb-0 text-center mt-2" id="place_order_section">
                                                            or
                                                            <button type="button" class="btn btn-primary place-new-order">Place new Order</button>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div id="check_message" class="mt-2"></div> <!-- Message display -->

                                        <!-- Warranty Form Section (Initially Hidden) -->
                                        <div id="warranty_section" class="row mt-3" style="display: none;">
                                            <!-- Category & Product in a Single Row -->
                                            <div class="col-md-6 col-12 mb-2">
                                                <label class="form-label" for="category_id">Category<span class="text-danger">*</span></label>
                                                <select class="form-select" name="category_id" id="category_id">
                                                    <option value="" selected disabled>Select Category</option>
                                                    @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6 col-12 mb-2">
                                                <label class="form-label" for="product_id">Product<span class="text-danger">*</span></label>
                                                <select class="form-select" name="product_id" id="product_id" disabled>
                                                    <option value="" selected disabled>Select Product</option>
                                                </select>
                                            </div>

                                            <!-- Customer Name & Customer Village in a Single Row -->
                                            <div class="col-md-6 col-12 mb-2">
                                                <label class="form-label" for="customer_name">Customer Name<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="customer_name" id="customer_name" placeholder="Enter Customer Name">
                                            </div>

                                            <div class="col-md-6 col-12 mb-2">
                                                <label class="form-label" for="customer_village">Customer Village<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="customer_village" id="customer_village" placeholder="Enter Customer Village">
                                            </div>

                                            <!-- Customer Mobile & Purchase Date in a Single Row -->
                                            <div class="col-md-6 col-12 mb-2">
                                                <label class="form-label" for="contact_number">Contact Number<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="contact_number" id="contact_number" placeholder="Enter Contact Number">
                                            </div>

                                            <div class="col-md-6 col-12 mb-2">
                                                <label class="form-label" for="purchase_date">Purchase Date & Time<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="purchase_date" name="purchase_date"
                                                    value="{{ now()->format('d/m/Y H:i:s') }}" readonly>
                                            </div>

                                            <div class="col-12 text-center mt-2 d-flex justify-content-center align-items-center gap-2">
                                                <!-- Warranty Duration Dropdown -->
                                                <select class="form-select w-auto" name="warranty_duration" id="warranty_duration">
                                                    <option value="2" selected>2 Years</option> <!-- Default Selected -->

                                                </select>

                                                <!-- Submit Button -->
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>


                                        </div> <!-- End Warranty Section -->
                                    </div>
                                </form>
                            </div> <!-- Card Body End -->
                        </div> <!-- Card End -->
                    </div> <!-- Col End -->
                </div> <!-- Row End -->
            </section>
        </div> <!-- Content Body End -->
    </div> <!-- Content Wrapper End -->
</div>
@endsection

@section('pagescript')
<script>
    // Hide section when close button is clicked
    document.getElementById('close_replace_item').addEventListener('click', function() {
        document.getElementById('replace_item_section').style.display = 'none';
    });
</script>
<script>
    $(document).ready(function() {
        // Load products dynamically based on category selection
        $('#category_id').on('change', function() {
            var categoryId = $(this).val();
            if (categoryId) {
                $.ajax({
                    url: "{{ route('get.products.by.category') }}",
                    type: "GET",
                    data: {
                        category_id: categoryId
                    },
                    success: function(data) {
                        $('#product_id').html(data).prop('disabled', false);
                    }
                });
            } else {
                $('#product_id').html('<option value="" selected disabled>Select Product</option>').prop('disabled', true);
            }
        });

        // Check Button Click
        $('.check-warranty').on('click', function() {
            $('#replace_item_section').hide();

            var serialNumber = $('#serial_number').val();
            $('#check_message').html(''); // Clear previous message
            $('#warranty_section').hide();
            $('.replace-item').hide();

            if (!serialNumber) {
                $('#check_message').html('<div class="p-1 rounded alert alert-danger">Please enter a serial number first.</div>');
                return;
            }

            $.ajax({
                url: "{{ route('check.serial.number') }}",
                type: "GET",
                data: {
                    serial_number: serialNumber
                },
                success: function(response) {

                    if (response.status === 'not_found') {
                        $('#check_message').html('<div class="p-1 rounded alert alert-danger">Serial number does not exist.</div>');
                        $('#warranty_section').hide();
                    } else if (response.status === 'assigned') {
                        //visible replace item button
                        $('.replace-item').show(); // ✅ Show the Replace Item button

                        $('#serial_no_id').val(response.id);

                        var validToDate = new Date(response.warranty.valid_to);
                        var currentDate = new Date();

                        if (validToDate >= currentDate) {
                            // in warranty
                            $('.replace-item').show();
                        } else {
                            $('.replace-item').hide();
                        }
                        var warrantyStatus = validToDate >= currentDate ?
                            '<span class="badge bg-success">In Warranty</span>' :
                            '<span class="badge bg-danger">Expired</span>';

                        $('#check_message').html(
                            `<div">
                                <div class="p-1 rounded alert alert-warning"><p class="fw-bold">This serial number is already assigned to another product.</p></div>
                                <table class="table table-bordered table-sm">
                                    <tbody>
                                        <tr>
                                            <td>Product</td>
                                            <th>${response.warranty.product.category.category_name} > ${response.warranty.product.product_name}</th>
                                        </tr>
                                        <tr>
                                            <td>Customer Name</td>
                                            <th>${response.warranty.cus_name}</th>
                                        </tr>
                                        <tr>
                                            <td>Customer Village</td>
                                            <th>${response.warranty.cus_village}</th>
                                        </tr>
                                        <tr>
                                            <td>Customer Mobile</td>
                                            <th>${response.warranty.cus_mobile}</th>
                                        </tr>
                                        <tr>
                                            <td>Registered Date</td>
                                            <th>${response.warranty.valid_from}</th>
                                        </tr>

                                        <tr>
                                            <td>Status</td>
                                            <th>${warrantyStatus}</th>
                                        </tr>

                                        <tr>
                                            <td>Remarks</td>
                                            <th>${response.warranty?.remarks ? response.warranty.remarks : ''}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>`
                        );

                        $('#warranty_section').hide();
                    } else if (response.status === 'available') {
                        $('#check_message').html('<div class="p-1 rounded alert alert-success">Serial number: ' + $('#serial_number').val() + ' is available. You can add warranty.</div>');
                        $('#warranty_section').show();
                        $('#serial_no_id').val(response.id);

                        if (response.warranty && response.warranty.product) {
                            let product = response.warranty.product;
                            let category = product.category;

                            // Prefill Category Dropdown
                            $('#category_id').val(category.id).trigger('change');

                            // Enable Product Dropdown
                            setTimeout(() => {
                                $('#product_id').val(product.id).trigger('change');
                            }, 1000);
                        }
                    }
                },
                error: function() {
                    $('#check_message').html('<div class="alert alert-danger">Error checking serial number. Please try again.</div>');
                }
            });
        });

        $('.replace-item').on('click', function() {
            $('#replace_item_section').show();
        });

        $('.save-warranty-new').on('click', function() {
            var newSerialNumber = $('#new_serial_number').val();
            var serialNoId = $('#serial_no_id').val();

            // if (!newSerialNumber) {
            //     $('#check_message').html('<div class="p-1 rounded alert alert-danger">Please enter a new serial first.</div>');
            //     return;
            // }

            $.ajax({
                url: "{{ route('check.save.serial.number') }}",
                type: "GET",
                data: {
                    new_serial_number: newSerialNumber,
                    old_serial_id: serialNoId
                },
                success: function(response) {
                    if (response.status === 'not_found') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Serial Number',
                            text: 'The entered serial number is invalid.',
                        });

                    } else if (response.status === 'assigned') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Already Assigned',
                            text: 'This serial number is used, Try new',
                        });

                    } else if (response.status === 'available') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Serial Assigned Successfully',
                            text: 'Serial number: ' + newSerialNumber + ' is successfully assigned.',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(); // Refreshes the page
                            }
                        });
                    }
                },
                error: function() {
                    // $('#check_message').html('<div class="alert alert-danger">Error checking new serial number. Please try again.</div>');
                }
            });
        });

        // **Handle Form Submission via AJAX**
        $('#warranty_form').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            $.ajax({
                url: "{{ route('warranty.store') }}",
                type: "POST",
                data: $(this).serialize(), // Serialize form data
                success: function(response) {
                    if (response.success) {
                        $('#check_message').html('<div class="alert alert-success">' + response.message + '</div>');
                        $('#warranty_form')[0].reset(); // Reset form
                        $('#warranty_section').hide();
                    } else {
                        $('#check_message').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '<div class="alert alert-danger"><ul>';
                    $.each(errors, function(key, value) {
                        errorMessage += '<li>' + value[0] + '</li>';
                    });
                    errorMessage += '</ul></div>';
                    $('#check_message').html(errorMessage);
                }
            });
        });

        // hide and display order new 
        $(document).ready(function() {
            let $serialInput = $("#new_serial_number");
            let $placeOrderSection = $("#place_order_section");

            function toggleElements() {
                if ($serialInput.val().trim() === "") {
                    $placeOrderSection.show();
                } else {
                    $placeOrderSection.hide();
                }
            }

            // Initial state check
            toggleElements();

            // Hide warning and "Place new Order" on input
            $serialInput.on("input", function() {
                toggleElements();
            });

            // Show them again if input is cleared
            $serialInput.on("blur", function() {
                toggleElements();
            });
        });

        // place new order for replace item
        $(document).on('click', '.place-new-order', function() {
            var serialNoId = $('#serial_no_id').val(); // Get Serial Number ID from input field

            if (!serialNoId) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please enter a valid Serial Number ID.'
                });
                return;
            }

            $.ajax({
                url: "{{ route('customerorder.replace') }}",
                type: 'POST',
                data: {
                    serial_no_id: serialNoId,
                    _token: $('meta[name="csrf-token"]').attr('content') // CSRF Token for security
                },
                beforeSend: function() {
                    $('.place-new-order').prop('disabled', true).text('Placing Order...');
                },
                success: function(response) {
                    if (response.status) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Order Created!',
                            text: 'Order ID: ' + response.order_id
                        }).then(() => {
                            location.reload(); // Reload page after confirmation
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseJSON);
                    Swal.fire({
                        icon: 'error',
                        text: 'Something went wrong. Please try again.'
                    });
                },
                complete: function() {
                    $('.place-new-order').prop('disabled', false).text('Place new Order');
                }
            });
        });
    });
</script>
@endsection