<!-- Order Confirmation -->
<div class="success-container">
    <div class="card">
        <div class="card-body">
            <h4>Thanks for your order!</h4>
            <div class="row m-0 mb-2">
                <h6>Order Number: #<span id="orderNumber"></span></h6>
            </div>
            <div class="row m-0">
                Your order has been received and will be delivered to you soon.
            </div>
            <div class="row m-0 mt-3">
                <div class="col">
                    <button class="btn btn-outline-dark btn-sm pull-right" id="continueShopping"
                            onclick="$('#continueShopping').trigger('click');">
                        <i class="fa fa-undo"></i>
                        Continue shopping
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>