@extends('layouts.app')
@section('title', 'Customer Home')

@section('content')
<div class="app-content content">
    <div class="col-md-10 content-wrapper container-xxl p-0">

        <!-- Category Filter -->
        <div class="d-flex flex-wrap gap-1 overflow-auto justify-content-center">
            <!-- <div class="card text-center shadow-sm bg-light border-0 p-1 gradient-border d-flex align-items-center justify-content-center flex-column category-filter"
                data-id="all" style="width: 120px; flex: 1 0 auto; cursor: pointer;">
                <h5 class="mt-0 mb-0 text-dark fw-bold">All</h5>
            </div> -->
            @foreach ($categories as $category)
            <div class="card text-center shadow-sm bg-light border-0 p-1 gradient-border 
                        d-flex align-items-center justify-content-center flex-column category-filter"
                data-id="{{ $category->id }}" style="width: 120px; flex: 1 0 auto; cursor: pointer;">
                <img src="{{ asset('upload/category/' . $category->image) }}"
                    alt="{{ $category->category_name }}"
                    class="img-fluid"
                    style="width: 80px; height: 80px; object-fit: cover;">
                <h5 class="mt-0 mb-0 text-dark fw-bold">{{ $category->category_name }}</h5>
            </div>
            @endforeach
        </div>

        <!-- Search Input -->
        <div class="mb-3 d-flex justify-content-end">
            <input type="text" id="searchProduct" class="form-control" placeholder="Search by Product Name or Code" style="width: 300px;">
        </div>

    </div>

    <!-- Products Section -->
    <div class="card col-md-12 container-xxl py-2" style="background-color:#f8f9fa;">
        <div class="row" id="productList">
            @foreach ($products as $product)
            <div class="col-md-3 col-6 mb-3 product-card ">

                <!-- Clickable Card (Opens Modal) -->
                <a href="#" class="text-center shadow-sm border-0 position-relative product-info text-decoration-none d-flex flex-column"
                    data-bs-toggle="modal"
                    data-bs-target="#productModal"
                    data-category-id="{{ $product->category_id }}"
                    data-category="{{ $product->category->category_name }}"
                    data-name="{{ $product->product_name }}"
                    data-code="{{ $product->product_code }}"
                    data-description="{{ $product->description }}"
                    data-box="{{ $product->box }}"
                    data-price="{{ number_format($product->price ?? 0, 2) }}"
                    data-image="{{ asset($product->image ? 'upload/product/' . $product->image : 'upload/product_default.png') }}"
                    data-image2="{{ $product->image2 ? asset('upload/product/' . $product->image2) : '' }}"
                    data-image3="{{ $product->image3 ? asset('upload/product/' . $product->image3) : '' }}">

                    <!-- Product Image -->
                    <img src="{{ asset($product->image ? 'upload/product/' . $product->image : 'upload/product_default.png') }}"
                    alt="{{ $product->product_name }}"
                    class="img-fluid w-100"
                    style="height: 150px; object-fit: contain; background-color:rgb(215, 218, 216); padding: 10px;" loading="lazy">


                    <!-- Product Code Label -->
                    <span class="position-absolute top-0 end-0 bg-primary text-white px-3 medium"
                        style="padding:5px; border-radius: 5px;">
                        {{ $product->product_code }}
                    </span>

                    <div class="card-body p-2">
                        <!-- Product Name -->
                        <h6 class="text-dark fw-bold mb-1">{{ $product->category->category_name .' > '. $product->product_name }}</h6>

                        <!-- Items per Box -->
                        <p class="text-muted small mb-0">Per Box: <strong>{{ $product->box }} items</strong></p>

                        <!-- Total Price -->
                        <p class="text-primary fw-bold mb-2">₹{{ number_format($product->price ?? 0, 2) }}/item</p>
                    </div>
                </a>

                <!-- Cart Actions -->
                <div class="cart-actions text-center">
                    @if ($product->cart_quantity > 0)
                    <!-- If product is already in cart, show increment/decrement buttons -->
                    <div id="cart-item-{{ $product->id }}" class="d-flex flex-column flex-sm-row align-items-center justify-content-between cart-ui w-100">
                        <span class="cart-total fw-bold">₹{{ number_format($product->price * $product->cart_quantity, 2) }}</span>
                        <div class="d-flex align-items-center">
                            <button class="cart-minus btn btn-sm btn-outline-primary px-2" data-id="{{ $product->id }}" data-box="{{ $product->box }}" data-price="{{ $product->price }}">-</button>
                            <span class="cart-quantity mx-2" data-id="{{ $product->id }}" data-box="{{ $product->box }}">{{ $product->cart_quantity }}</span>
                            <button class="cart-plus btn btn-sm btn-outline-primary px-2" data-id="{{ $product->id }}" data-box="{{ $product->box }}" data-price="{{ $product->price }}">+</button>
                        </div>
                    </div>
                    @else
                    <!-- Otherwise, show "Add to Cart" button -->
                    <button class="btn btn-sm w-100 bg-primary text-white add-to-cart-btn"
                        data-id="{{ $product->id }}"
                        data-box="{{ $product->box }}"
                        data-price="{{ $product->price }}">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    @endif
                </div>

            </div>
            @endforeach
        </div>

        <!-- No Data Found Message -->
        <div id="noDataMessage" class="text-center text-muted py-3" style="display: none;">
            No products found.
        </div>
    </div>


    <!-- Bootstrap Modal for Product Description -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="d-flex flex-column align-items-center">
                        <!-- Product Images Slider -->
                        <div id="modalImageSlider" class="mb-3" style="max-width: 250px; position: relative;">
                            <img id="modalSliderImage" src="" alt="Product Image" class="img-fluid mb-1 rounded" style="max-height: 200px; object-fit: contain;">
                            <div id="modalSliderIndicators" class="slider-indicators-bar"></div>
                            <div class="d-flex justify-content-between mt-1">
                                <button id="modalSliderPrev" class="btn btn-sm btn-outline-secondary">&lt;</button>
                                <button id="modalSliderNext" class="btn btn-sm btn-outline-secondary">&gt;</button>
                            </div>
                        </div>
                        <!-- Product Details -->
                        <div class="d-flex w-100">
                            <div class="col-md-6 text-start">
                                <p><strong>Category:</strong> <span id="modalCategory"></span></p>
                                <p><strong>Product name:</strong> <span id="productModalLabel1"></span></p>
                                <p><strong>Product Code:</strong> <span id="modalCode"></span></p>
                                <p><strong>Per Box:</strong> <span id="modalBox"></span> items</p>
                                <p class="text-primary fw-bold">₹<span id="modalPrice"></span>/item</p>
                            </div>
                            <div class="col-md-6 text-start">
                                <p><strong>Description:</strong> <span id="productDescription"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('pagescript')
<script>
    function updateCartView() {
        $.ajax({
            url: "{{ route('cart.view') }}",
            method: "GET",
            success: function(response) {
                let cartContent = $(response).find('.p-0.flex-grow-1').html();
                $('#cartSidebar .p-0.flex-grow-1').html(cartContent);


            },
            error: function(xhr) {
                console.error("Error updating cart:", xhr.responseText);
            }
        });
    }

    // category and filter code
    $(document).ready(function() {
        $(".category-filter").on("click", function() {
            let isActive = $(this).hasClass("bg-primary");

            // Reset all category buttons
            $(".category-filter").removeClass("bg-primary text-white");
            $(".category-filter h5").removeClass("text-white").addClass("text-dark");

            // Toggle selection
            if (!isActive) {
                $(this).addClass("bg-primary text-white");
                $(this).find("h5").removeClass("text-dark").addClass("text-white");
            }

            filterProducts();
        });

        $("#searchProduct").on("keyup", function() {
            filterProducts();
        });

        function filterProducts() {
            let selectedCategory = $(".category-filter.bg-primary").data("id");
            let searchText = $("#searchProduct").val().toLowerCase();

            $(".product-card").hide().filter(function() {
                let $productInfo = $(this).find(".product-info"); // Select the <a> element inside .product-card

                let categoryMatch = !selectedCategory || $productInfo.data("category-id") == selectedCategory;

                let name = $productInfo.data("name").toLowerCase();
                let code = $productInfo.data("code").toString();
                let searchMatch = name.includes(searchText) || code.includes(searchText);
                return categoryMatch && searchMatch;
            }).fadeIn();

            checkNoData();
        }

        function checkNoData() {
            if ($(".product-card:visible").length === 0) {
                $("#noDataMessage").fadeIn();
            } else {
                $("#noDataMessage").fadeOut();
            }
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        let productModal = document.getElementById("productModal");

        productModal.addEventListener("show.bs.modal", function(event) {
            let button = event.relatedTarget; // The clicked product card
            let modalTitle = document.getElementById("productModalLabel");
            let modalTitle2 = document.getElementById("productModalLabel1");
            let modalCategory = document.getElementById("modalCategory");
            let modalCode = document.getElementById("modalCode");
            let modalDescription = document.getElementById("productDescription");
            let modalBox = document.getElementById("modalBox");
            let modalPrice = document.getElementById("modalPrice");

            // Set data inside the modal
            modalTitle.textContent = button.getAttribute("data-name");
            modalTitle2.textContent = button.getAttribute("data-name");
            modalCategory.textContent = button.getAttribute("data-category");
            modalCode.textContent = button.getAttribute("data-code");
            modalDescription.textContent = button.getAttribute("data-description");
            modalBox.textContent = button.getAttribute("data-box");
            modalPrice.textContent = button.getAttribute("data-price");

            // Image slider logic
            let images = [];
            let img1 = button.getAttribute("data-image");
            let img2 = button.getAttribute("data-image2");
            let img3 = button.getAttribute("data-image3");
            if (img1) images.push(img1);
            if (img2) images.push(img2);
            if (img3) images.push(img3);
            let currentIndex = 0;
            let sliderImg = document.getElementById("modalSliderImage");
            let prevBtn = document.getElementById("modalSliderPrev");
            let nextBtn = document.getElementById("modalSliderNext");
            function updateSlider() {
                sliderImg.src = images[currentIndex] || '';
                prevBtn.disabled = images.length <= 1;
                nextBtn.disabled = images.length <= 1;
                // Update indicators
                let indicatorsHtml = '';
                for (let i = 0; i < images.length; i++) {
                    indicatorsHtml += `<span class="slider-dot${i === currentIndex ? ' active' : ''}"></span>`;
                }
                document.getElementById('modalSliderIndicators').innerHTML = indicatorsHtml;
            }
            updateSlider();
            prevBtn.onclick = function() {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                updateSlider();
            };
            nextBtn.onclick = function() {
                currentIndex = (currentIndex + 1) % images.length;
                updateSlider();
            };
        });
    });

    // add to card at same space
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".qty-decrease").forEach(function(btn) {
            btn.addEventListener("click", function() {
                let input = this.nextElementSibling;
                let boxSize = parseInt(input.dataset.box);
                let currentQty = parseInt(input.value);

                if (currentQty > boxSize) {
                    input.value = currentQty - boxSize;
                }
            });
        });

        document.querySelectorAll(".qty-increase").forEach(function(btn) {
            btn.addEventListener("click", function() {
                let input = this.previousElementSibling;
                let boxSize = parseInt(input.dataset.box);
                let currentQty = parseInt(input.value);

                input.value = currentQty + boxSize;
            });
        });

        document.querySelectorAll(".add-to-cart").forEach(function(button) {
            button.addEventListener("click", function() {
                let parent = this.previousElementSibling;
                let qtyInput = parent.querySelector(".qty-input");
                let quantity = parseInt(qtyInput.value);
                let productId = this.getAttribute("data-id");

                console.log(`Product ID: ${productId}, Quantity: ${quantity}`);
                // Call your add-to-cart API or function here
            });
        });
    });

    // add to card button
    $(document).ready(function() {
        // Function to call API for cart updates
        function updateCart(productId, qty) {
            $.ajax({
                url: "{{ route('cart.manage') }}", // Update this URL as needed
                method: "POST",
                data: {
                    product_id: productId,
                    quantity : qty,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (!$(".toast-success").length) {
                        toastr.success(response.message, 'Success');
                    }
                    updateCartView(); // Refresh cart dynamically
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        alert('Please log in to add products to your cart.');
                        window.location.href = "{{ route('login') }}";
                    }
                }
            });
        }

        // Event delegation for dynamically added "Add to Cart" button
        $(document).on("click", ".add-to-cart-btn", function() {
            let productId = $(this).data("id");
            let productBox = parseInt($(this).data("box")) || 1;
            let productPrice = parseFloat($(this).data("price")) || 0;

            // Check if already in cart
            if ($("#cart-item-" + productId).length) {
                return;
            }

            // Initial quantity and total price
            let initialQuantity = productBox;
            let totalPrice = (initialQuantity * productPrice).toFixed(2);

            // Show animation only for first click
            let cartIcon = $(this).find("i");
            let cartButton = $("#cartButton");
            if (!$(this).data("animated")) {
                $(this).data("animated", true);

                // Clone the cart icon
                let flyingIcon = $("<div class='animated-cart'><i class='fas fa-shopping-cart'></i></div>").appendTo("body");

                // Get positions for animation
                let startOffset = cartIcon.offset();
                let endOffset = cartButton.offset();

                // Set initial position of cloned icon
                flyingIcon.css({
                    position: "absolute",
                    top: startOffset.top,
                    left: startOffset.left,
                    opacity: 1,
                });

                // Animate towards the cart button
                flyingIcon.animate({
                        top: endOffset.top + 10,
                        left: endOffset.left + 10,
                        width: 20,
                        height: 20,
                        opacity: 0,
                    },
                    800,
                    function() {
                        $(this).remove();
                    }
                );
            }

            // Replace button with cart UI
            $(this).replaceWith(`
                <div id="cart-item-${productId}" class="d-flex flex-column flex-sm-row align-items-center justify-content-between cart-ui w-100">
                    <span class="cart-total fw-bold" data-id="${productId}">₹${totalPrice}</span>
                    <div class="d-flex align-items-center">
                        <div class="cart-minus btn btn-sm btn-outline-primary px-2" data-id="${productId}" data-box="${productBox}" data-price="${productPrice}">-</div>
                        <span class="cart-quantity mx-2" data-id="${productId}" data-box="${productBox}">${initialQuantity}</span>
                        <div class="cart-plus btn btn-sm btn-outline-primary px-2" data-id="${productId}" data-box="${productBox}" data-price="${productPrice}">+</div>
                    </div>
                </div>
            `);
            // API Call for adding to cart
            updateCart(productId, productBox);
        });

        // In the JS, delegate plus/minus events from #dashboard-products
        $('#productList').on("click", ".cart-plus", function() {
            let productId = $(this).data("id");
            let productPrice = parseFloat($(this).data("price")) || 0;
            let boxSize = parseInt($(this).data("box")) || 1;

            // Scoped selector: find elements relative to the clicked button
            let container = $(this).closest('.cart-ui, .add-to-cart-btn').parent();
            let quantityElement = $(this).siblings('.cart-quantity');
            let totalElement = container.find(".cart-total");

            // Defensive: strip non-digits before parsing
            let currentQuantityRaw = quantityElement.text();
            let currentQuantity = parseInt(currentQuantityRaw.replace(/[^0-9]/g, ''), 10) || 0;
            let newQuantity = currentQuantity + boxSize;
            let newTotal = (newQuantity * productPrice).toFixed(2);

            quantityElement.text(newQuantity);
            if (totalElement.length) {
                totalElement.text(`₹${newTotal}`);
            }

            // API Call
            updateCart(productId, newQuantity);
        });

        $('#productList').on("click", ".cart-minus", function() {
            let productId = $(this).data("id");
            let productPrice = parseFloat($(this).data("price")) || 0;
            let boxSize = parseInt($(this).data("box")) || 1;

            // Scoped selector: find elements relative to the clicked button
            let container = $(this).closest('.cart-ui');
            let quantityElement = $(this).siblings('.cart-quantity');
            let totalElement = container.find(".cart-total");

            // Defensive: strip non-digits before parsing
            let currentQuantityRaw = quantityElement.text();
            let currentQuantity = parseInt(currentQuantityRaw.replace(/[^0-9]/g, ''), 10) || 0;
            let newQuantity = currentQuantity - boxSize;
            let newTotal = (newQuantity * productPrice).toFixed(2);

            if (newQuantity <= 0) {
                container.replaceWith(`
                    <button class="btn btn-sm w-100 bg-primary text-white add-to-cart-btn"
                        data-id="${productId}" data-box="1" data-price="${productPrice}">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                `);

                // API Call
                updateCart(productId, 0);
            } else {
                quantityElement.text(newQuantity);
                if (totalElement.length) {
                    totalElement.text(`₹${newTotal}`);
                }

                // API Call
                updateCart(productId, newQuantity);
            }
        });
    });

    // add to card button
    // $(document).ready(function() {
    //     // Event delegation for dynamically added "Add to Cart" button
    //     $(document).on("click", ".add-to-cart-btn", function() {
    //         let productId = $(this).data("id");
    //         let productBox = parseInt($(this).data("box")) || 1; // Default to 1 if missing
    //         let productPrice = parseFloat($(this).data("price")) || 0;

    //         // Check if already in cart
    //         if ($("#cart-item-" + productId).length) {
    //             return;
    //         }

    //         // Initial quantity and total price
    //         let initialQuantity = productBox;
    //         let totalPrice = (initialQuantity * productPrice).toFixed(2);

    //         // Replace button with cart UI
    //         $(this).replaceWith(`
    //         <div id="cart-item-${productId}" class="d-flex align-items-center justify-content-between cart-ui w-100">
    //             <span class="cart-total fw-bold" data-id="${productId}">₹${totalPrice}</span>
    //             <div class="d-flex align-items-center">
    //                 <div class="cart-minus btn btn-sm btn-outline-primary px-2" data-id="${productId}" data-box="${productBox}" data-price="${productPrice}">-</div>
    //                 <span class="cart-quantity mx-2" data-id="${productId}" data-box="${productBox}">${initialQuantity}</span>
    //                 <div class="cart-plus btn btn-sm btn-outline-primary px-2" data-id="${productId}" data-box="${productBox}" data-price="${productPrice}">+</div>
    //             </div>
    //         </div>
    //     `);
    //     });

    //     // Increase quantity by box size & update total
    //     $(document).on("click", ".cart-plus", function() {
    //         let productId = $(this).data("id");
    //         let productBox = parseInt($(this).data("box")) || 1;
    //         let productPrice = parseFloat($(this).data("price")) || 0;

    //         let quantityElement = $(".cart-quantity[data-id='" + productId + "']");
    //         let totalElement = $(".cart-total[data-id='" + productId + "']");

    //         let newQuantity = parseInt(quantityElement.text()) + productBox;
    //         let newTotal = (newQuantity * productPrice).toFixed(2);

    //         quantityElement.text(newQuantity);
    //         totalElement.text(`₹${newTotal}`);
    //     });

    //     // Decrease quantity, update total, restore "Add to Cart" when 0
    //     $(document).on("click", ".cart-minus", function() {
    //         let productId = $(this).data("id");
    //         let productBox = parseInt($(this).data("box")) || 1;
    //         let productPrice = parseFloat($(this).data("price")) || 0;

    //         let quantityElement = $(".cart-quantity[data-id='" + productId + "']");
    //         let totalElement = $(".cart-total[data-id='" + productId + "']");

    //         let newQuantity = parseInt(quantityElement.text()) - productBox;
    //         let newTotal = (newQuantity * productPrice).toFixed(2);

    //         if (newQuantity <= 0) {
    //             $("#cart-item-" + productId).replaceWith(`
    //             <button class="btn btn-sm w-100 bg-primary text-white add-to-cart-btn"
    //                 data-id="${productId}" data-box="${productBox}" data-price="${productPrice}">
    //                 <i class="fas fa-shopping-cart"></i> Add to Cart
    //             </button>
    //         `);
    //         } else {
    //             quantityElement.text(newQuantity);
    //             totalElement.text(`₹${newTotal}`);
    //         }
    //     });
    // });
</script>

<style>
    .add-to-cart {
        position: relative;
        overflow: hidden;
    }

    .add-to-cart::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.2);
        transition: left 0.3s ease-in-out;
    }

    .add-to-cart:hover::before {
        left: 100%;
    }

    .animated-cart {
        position: absolute;
        z-index: 1000;
        width: 40px;
        /* Increase initial size */
        height: 40px;
        color: #fff;
        /* Ensure it's white or any visible color */
        background-color: rgba(var(--bs-danger-rgb));
        /* Blue background */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        /* Add a shadow */
        transition: transform 0.8s ease-in-out, opacity 0.8s ease-in-out;
    }

    .product-card a {
        background-color: #ffffff;
        /* White card background */
        border-radius: 8px;
        /* Rounded corners */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Subtle shadow */
        transition: box-shadow 0.2s ease-in-out;
    }

    .product-card a:hover {
        /* transform: scale(1.05); */
        /* Slight zoom effect (if needed) */
        border-radius: 8px;
        /* Ensure border-radius remains */
        box-shadow: 0 8px 8px rgba(0, 0, 0, 0.15);
        /* Stronger shadow */
    }

    .qty-input {
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .qty-decrease,
    .qty-increase {
        width: 30px;
        height: 30px;
        font-size: 18px;
        padding: 0;
        line-height: 1;
    }

    .add-to-cart {
        margin-top: 5px;
    }

    .slider-indicators-bar {
      position: absolute;
      left: 50%;
      bottom: 18px;
      transform: translateX(-50%);
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 6px 18px;
      border-radius: 18px;
      background: rgba(255,255,255,0.7);
      box-shadow: 0 2px 12px rgba(0,0,0,0.12);
      backdrop-filter: blur(4px);
      z-index: 3;
    }
    .slider-dot {
      display: inline-block;
      width: 14px;
      height: 14px;
      border-radius: 50%;
      background: #e0e0e0;
      margin: 0 6px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.08);
      opacity: 0.7;
      transition: background 0.3s, transform 0.2s, opacity 0.2s;
    }
    .slider-dot.active {
      background: var(--bs-primary, #007bff);
      opacity: 1;
      transform: scale(1.25);
      box-shadow: 0 2px 8px rgba(0,0,0,0.18);
    }
</style>
@endsection