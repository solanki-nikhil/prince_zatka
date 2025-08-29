<div id="cartSidebar" class="position-fixed end-0 top-0 vh-100 bg-white shadow-lg d-flex flex-column"
    style="max-width: 90vw; width: 350px; transform: translateX(100%); transition: transform 0.3s ease-in-out; z-index: 1051;">

    <!-- Cart Header -->
    <div class="p-2 border-bottom d-flex justify-content-between align-items-center bg-light">
        <h5 class="mb-0">Your Cartss</h5>
        <button id="closeCart" class="btn-close"></button>
    </div>

    <!-- Cart Items + Footer (Single flex container) -->
    <div class="p-0 flex-grow-1 d-flex flex-column" style="min-height: 0;">
        <!-- Cart Items (Scrollable) -->
        <div class="flex-grow-1 overflow-auto">
            @if(count($cartItems) > 0)
            <table class="table align-middle w-100 cart-table">
                <tbody>
                    @foreach ($cartItems as $item)
                    <tr>
                        <td width="40">
                            <!-- <img src="{{ asset($item->product->image ? 'upload/product/' . $item->product->image : 'upload/product_default.png') }}"
                                class="rounded" width="40" height="40" style="object-fit: cover;" alt="Product"> -->

                                <img src="{{ asset($item->product->image ? 'upload/product/' . $item->product->image : 'upload/product_default.png') }}"
                                class="rounded"
                                width="40"
                                height="40"
                                style="object-fit: contain; "
                                alt="Product">

                        </td>
                        <td>
                            <div>
                                <h6 class="mb-0">{{ $item->product->category->category_name.' > '.$item->product->product_name }}</h6>
                            </div>
                            <div>
                                <small class="text-primary">{{ $item->quantity }} items ({{ $item->product->box }} items/box)</small>
                            </div>
                            <div>
                                <small class="text-primary">₹{{ number_format($item->product->price, 2) }}/item</small>
                            </div>
                            <div>
                                <span class="cart-total fw-bold" data-id="{{ $item->product_id }}">₹{{ number_format($item->product->price * $item->quantity, 2) }}</span>
                            </div>
                            <div class="d-flex align-items-center mt-1">
                                <button class="cart-minus btn btn-sm btn-outline-primary px-2" data-id="{{ $item->product_id }}" data-box="{{ $item->product->box }}" data-price="{{ $item->product->price }}">-</button>
                                <span class="cart-quantity mx-2" data-id="{{ $item->product_id }}" data-box="{{ $item->product->box }}">{{ $item->quantity }}</span>
                                <button class="cart-plus btn btn-sm btn-outline-primary px-2" data-id="{{ $item->product_id }}" data-box="{{ $item->product->box }}" data-price="{{ $item->product->price }}">+</button>
                            </div>
                        </td>
                        <td>
                            <!-- No plus/minus here -->
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger remove-item" data-id="{{ $item->id }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="p-3 text-center text-muted">Your cart is empty.</p>
            @endif
        </div>

        <!-- Cart Footer (Sticks to bottom inside .flex-grow-1) -->
        <div class="p-2 border-top bg-light mt-auto">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <strong>Total:</strong>
                <strong class="text-primary">₹<span id="cartTotal" class="cartTotal">{{ number_format($total, 2) }}</span></strong>
            </div>
            <button id="orderButton" class="btn btn-danger w-100 orderButton">Order</button>
        </div>
    </div>
</div>

@section('pagescript')
<script>
$(document).ready(function() {
    console.log('[CART SIDEBAR] Script loaded');
    console.log('[CART SIDEBAR] .cart-plus buttons at load:', $('.cart-plus').length);
    // Plus button
    $('#cartSidebar').on('click', '.cart-plus', function() {
        let productId = String($(this).data('id'));
        let productPrice = parseFloat($(this).data('price')) || 0;
        let quantityElement = $(this).siblings('.cart-quantity');
        let totalElement = $(".cart-total[data-id='" + productId + "']");
        let currentQuantityRaw = quantityElement.text();
        let currentQuantity = parseInt(currentQuantityRaw.replace(/[^0-9]/g, ''), 10) || 0;
        let newQuantity = currentQuantity + 1;
        let newTotal = (newQuantity * productPrice).toFixed(2);
        console.log('[CART SIDEBAR] PLUS clicked', {productId, currentQuantity, newQuantity});
        quantityElement.text(newQuantity);
        totalElement.text(`₹${newTotal}`);
        updateCart(productId, newQuantity);
    });

    // Minus button
    $('#cartSidebar').on('click', '.cart-minus', function() {
        let productId = String($(this).data('id'));
        let productPrice = parseFloat($(this).data('price')) || 0;
        let quantityElement = $(this).siblings('.cart-quantity');
        let totalElement = $(".cart-total[data-id='" + productId + "']");
        let currentQuantityRaw = quantityElement.text();
        let currentQuantity = parseInt(currentQuantityRaw.replace(/[^0-9]/g, ''), 10) || 0;
        let newQuantity = currentQuantity - 1;
        let newTotal = (newQuantity * productPrice).toFixed(2);
        console.log('[CART SIDEBAR] MINUS clicked', {productId, currentQuantity, newQuantity});
        if (newQuantity <= 0) {
            $(this).parent().remove();
            totalElement.text(`₹0.00`);
            updateCart(productId, 0);
        } else {
            quantityElement.text(newQuantity);
            totalElement.text(`₹${newTotal}`);
            updateCart(productId, newQuantity);
        }
    });

    function updateCart(productId, qty) {
        console.log('[CART SIDEBAR] updateCart called', {productId, qty});
        $.ajax({
            url: "{{ route('cart.manage') }}",
            method: "POST",
            data: {
                product_id: productId,
                quantity: qty,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                console.log('[CART SIDEBAR] updateCart AJAX success', response);
                if (!$('.toast-success').length) {
                    toastr.success(response.message, 'Success');
                }
                // Optionally update cart view
                if (typeof updateCartView === 'function') {
                    updateCartView();
                } else {
                    location.reload();
                }
            },
            error: function(xhr) {
                console.log('[CART SIDEBAR] updateCart AJAX error', xhr);
                if (xhr.status === 403) {
                    alert('Please log in to update your cart.');
                    window.location.href = "{{ route('login') }}";
                }
            }
        });
    }
});
</script>
@endsection