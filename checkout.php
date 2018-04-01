<?php

require './database/vendor/autoload.php';
require './database/Database.php';

session_start();

/**
 * $isLoggedIn: A boolean returned by isset is assigned.
 */
$isLoggedIn = isset($_SESSION['userID']);

/**
 * The user's cart is fetched.
 */
$db = new Database();
$cart = $db->fetchCart(['userID' => ($isLoggedIn) ? $_SESSION['userID'] : $_SESSION['guestID']], false);
$isEmptyCart = sizeof($cart['items']) === 0;

/**
 * getTable: Returns html for a table containing the user's cart items.
 */
function getTable($type) {
    global $isLoggedIn;
    global $isEmptyCart;

    $db = new Database();
    $cart = $db->fetchCart(['userID' => ($isLoggedIn) ? $_SESSION['userID'] : $_SESSION['guestID']], false);

    $html = '';

    /**
     * If the cart is not empty, a shopping cart table is appended to $html.
     */
    if (!$isEmptyCart) {
        for ($i = 0; $i < sizeof($cart['items']); $i++) {
            $title = $cart['items'][$i]['title'] . ' (' . $cart['items'][$i]['year'] . ')';
            $price = ['dvdPrice' => $cart['items'][$i]['dvdPrice'], 'bluRayPrice' => $cart['items'][$i]['bluRayPrice']];
            $quantity = ['dvdQuantity' => (int)$cart['items'][$i]['dvdQuantity'], 'bluRayQuantity' => (int)$cart['items'][$i]['bluRayQuantity']];

            foreach ($quantity as $key => $value) {
                $isDVD = ($key) === 'dvdQuantity';

                /**
                 * If the value of a key in quantity is greater than zero, the product is appended to $html.
                 */
                if ($value > 0) {
                    if ($type === 'shoppingCart') {
                        $html .= '<tr id="' . $cart['items'][$i]['_id'] . '">
                                <td valign="middle" class="checkout-row">' . $title . '</td>
                                <td valign="middle" class="checkout-row">' . (($isDVD) ? 'DVD' : 'Blu-Ray') . '</td>
                                <td valign="middle" class="checkout-row">' . $value . '</td>
                                <td valign="middle" class="checkout-row">' . '£' . (($isDVD) ? $price['dvdPrice'] : $price['bluRayPrice']) . '</td>
                            </tr>';
                    } else if ($type === 'review') {
                        $html .= '<tr>
                                <td valign="middle" class="checkout-row">' . $title . '</td>
                                <td valign="middle" class="checkout-row">' . (($isDVD) ? 'DVD' : 'Blu-Ray') . '</td>
                                <td valign="middle" class="checkout-row">' . $value . '</td>
                                <td valign="middle" class="checkout-row">' . '£' . (($isDVD) ? $price['dvdPrice'] : $price['bluRayPrice']) . '</td>
                              </tr>';
                    }
                }
            }
        }
    }
    /**
     * If the shopping cart is empty, an alert is displayed on the page.
     */
    else if ($type === 'shoppingCart' && $isEmptyCart) {
        $html .= '<div class="alert alert-danger" role="alert">Your shopping cart is empty.</div>';
    }

    echo $html;
}

/**
 * getPrice: Returns html for a column containing a price breakdown.
 */
function getPrice($type) {
    global $isLoggedIn;
    global $isEmptyCart;

    $totalCost = 0;

    $db = new Database();
    $cart = $db->fetchCart(['userID' => ($isLoggedIn) ? $_SESSION['userID'] : $_SESSION['guestID']], false);

    /**
     * Sums the cost of each product in the user's cart.
     */
    for ($i = 0; $i < sizeof($cart['items']); $i++) {
        $quantity = ['dvdQuantity' => (int)$cart['items'][$i]['dvdQuantity'], 'bluRayQuantity' => (int)$cart['items'][$i]['bluRayQuantity']];

        foreach ($quantity as $key => $value) {
            $isDVD = ($key) === 'dvdQuantity';

            if ($value > 0) {
                $totalCost += ($value * (float)$cart['items'][$i][($isDVD) ? 'dvdPrice' : 'bluRayPrice']);
            }
        }
    }

    /**
     * The total cost summary is calculated.
     */
    $priceBreakdown = ['Subtotal (excl. VAT):' => '£' . bcdiv($totalCost, 1, 2),
        'VAT (20%):' => '£' . bcdiv((0.20 * $totalCost), 1, 2),
        'Post & Packaging:' => ($isEmptyCart) ? '£0.00' : 'Free',
        'Grand Total:' => '£' . bcdiv(($totalCost + (0.20 * $totalCost)), 1, 2)
    ];

    $html = '';

    /**
     * Appends a row containing each $priceBreakdown key-value.
     */
    foreach ($priceBreakdown as $key => $value) {
        if ($type === 'shoppingCart') {
            $html .= '<div class="row ' . (($key === 'Grand Total:') ? 'mt-3' : '') . '">
                            <div class="col-10 checkout-summary">
                                ' . (($key === 'Grand Total:') ? ('<h5>' . $key . '</h5>') : '<h6>' . $key . '</h6>') .
                '</div>
                            <div class="col-2 checkout-summary">
                                <h6 class="normal">' . $value . '</h6>
                            </div>
                        </div>';
        }
        else if ($type === 'review' && !$isEmptyCart) {
            if ($key === 'Grand Total:') {
                $html .= '<div class="row mt-3">' .
                    '<div class="col-8 checkout-summary">' .
                    '<h5>' . $key . '</h5>' .
                    '</div>' .
                    '<div class="col text-right checkout-summary">' .
                    '<h5>' . $value . '</h5>' .
                    '</div></div>';
            }
            else {
                $html .= '<div class="row">' .
                    '<div class="col-8 ' . (($key === 'Post & Packaging:') ? '' : 'checkout-summary') . '">' . $key . '</div>' .
                    '<div class="col text-right ' . (($key === 'Post & Packaging:') ? '' : 'checkout-summary') . '">' . $value . '</div>' .
                    '</div>';
            }
        }
    }

    echo $html;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS, Styles & Font Awesome-->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" type="text/css" href="libraries/font-awesome/css/font-awesome.min.css">

    <title>MovieHub | Checkout</title>
</head>
<body>

<?php require 'includes/header.php'; ?>

<div class="loading" id="loading"><i class="fa fa-circle-o-notch fa-spin"></i></div>

<?php

/**
 * The tabs which allow the user to navigate through different instances of the checkout
 * are displayed if the cart is not empty.
 */
echo ($isEmptyCart) ? '' : '
<!-- Shopping Cart, Shipping & Billing, Payment & Review & Place Order Tabs -->
<div class="checkout-steps" id="checkoutSteps">
    <div class="container">
        <ul class="nav justify-content-center">
            <!-- Shopping Cart Tab -->
            <li class="nav-item">
                <a class="nav-link active-step" id="shoppingCartTab" href="#navShoppingCart" role="tab" data-toggle="tab">
                    <div class="checkout-icon-border mr-2">
                        1
                    </div>
                    Shopping Cart
                </a>
            </li>
            <!-- Shipping & Billing Tab -->
            <li class="nav-item">
                <a class="nav-link inactive-step" id="shippingAndBillingTab" href="#navShippingAndBilling" role="tab"
                   data-toggle="tab">
                    <div class="checkout-icon-border mr-2">
                        2
                    </div>
                    Shipping & Billing
                </a>
            </li>
            <!-- Payment Tab -->
            <li class="nav-item">
                <a class="nav-link inactive-step disabled" id="paymentTab" href="#navPayment" role="tab"
                   data-toggle="tab">
                    <div class="checkout-icon-border mr-2">
                        3
                    </div>
                    Payment
                </a>
            </li>
            <!-- Review & Place Order Tab -->
            <li class="nav-item">
                <a class="nav-link inactive-step disabled" id="reviewAndPlaceOrderTab" href="#navReviewAndPlaceOrder"
                   role="tab"
                   data-toggle="tab">
                    <div class="checkout-icon-border mr-2">
                        4
                    </div>
                    Review & Place Order
                </a>
            </li>
        </ul>
    </div>
</div>';

?>

<!-- Tab Content -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-7">
            <div class="tab-content mt-3" id="checkoutTabContent">

                <!-- Shopping Cart -->
                <div class="tab-pane active" id="navShoppingCart" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col">
                            <h5 class="ml-0">Shopping Cart</h5>
                        </div>
                        <div class="col">
                            <button class="btn btn-outline-dark btn-sm pull-right" id="continueShopping">
                                <i class="fa fa-undo"></i>
                                Continue shopping
                            </button>
                        </div>
                    </div>
                    <!-- Shopping Cart Table -->
                    <table class="table table-hover checkout-table">
                        <thead>
                        <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Format</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Price</th>
                        <tbody id="cartContent">
                        <?php

                        /**
                         * The shopping cart table is displayed if the user's shopping cart isn't empty.
                         */
                        getTable('shoppingCart')

                        ?>
                        </tbody>
                    </table>
                    <!-- Shopping Cart Price -->
                    <div class="col text-right mt-3 pr-0">
                        <?php

                        /**
                         * The price breakdown column is displayed if the user's shopping cart isn't empty.
                         */
                        getPrice('shoppingCart')

                        ?>
                    </div>
                </div>

                <!-- Shipping & Billing -->
                <div class="tab-pane" id="navShippingAndBilling" role="tabpanel">
                    <div class="row">
                        <!-- Shipping Column -->
                        <div class="col mr-1">
                            <div class="row">
                                <div class="col">
                                    <h5 class="ml-0 mb-3">Shipping</h5>
                                </div>
                            </div>
                            <!-- Shipping Form -->
                            <div class="card">
                                <div class="card-body">
                                    <form id="shippingForm">
                                        <div class="form-row mb-3">
                                            <div class="col-6">
                                                <label for="shippingFirstName">First Name</label>
                                                <input type="text" class="form-control" id="shippingFirstName" required>
                                                <div class="invalid-feedback" id="invalidShippingFirstName"></div>
                                            </div>
                                            <div class="col-6">
                                                <label for="shippingLastName">Last Name</label>
                                                <input type="text" class="form-control" id="shippingLastName" required>
                                                <div class="invalid-feedback" id="invalidShippingLastName"></div>
                                            </div>
                                        </div>
                                        <div class="form-row mb-3">
                                            <div class="col">
                                                <label for="shippingMobileNumber">Mobile Number</label>
                                                <input type="text" class="form-control" id="shippingMobileNumber"
                                                       required>
                                                <div class="invalid-feedback" id="invalidShippingMobileNumber"></div>
                                            </div>
                                        </div>
                                        <div class="form-row mb-3">
                                            <div class="col-6">
                                                <label for="shippingAddress1">Address (Line 1)</label>
                                                <input type="text" class="form-control" id="shippingAddress1" required>
                                                <div class="invalid-feedback" id="invalidShippingAddress1"></div>
                                            </div>
                                            <div class="col-6">
                                                <label for="shippingAddress2">Address (Line 2)</label>
                                                <input type="text" class="form-control" id="shippingAddress2">
                                                <div class="invalid-feedback" id="invalidShippingAddress2"></div>
                                            </div>
                                        </div>
                                        <div class="form-row mb-3">
                                            <div class="col-6">
                                                <label for="shippingTownOrCity">Town/City</label>
                                                <input type="text" class="form-control" id="shippingTownOrCity" required>
                                                <div class="invalid-feedback" id="invalidShippingTownOrCity"></div>
                                            </div>
                                            <div class="col-6">
                                                <label for="shippingCounty">County</label>
                                                <input type="text" class="form-control" id="shippingCounty" required>
                                                <div class="invalid-feedback" id="invalidShippingCounty"></div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-7">
                                                <label for="shippingCountry">Country</label>
                                                <select class="custom-select" id="shippingCountry" required>
                                                    <option selected>United Kingdom</option>
                                                </select>
                                            </div>
                                            <div class="col-5">
                                                <label for="shippingPostCode">Post Code</label>
                                                <input type="text" class="form-control" id="shippingPostCode" required>
                                                <div class="invalid-feedback" id="invalidShippingPostCode"></div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Column -->
                        <div class="col mr-15">
                            <div class="row">
                                <div class="col padding-0">
                                    <h5 class="ml-0 mb-3">Billing</h5>
                                </div>
                                <div class="col padding-0">
                                    <div class="custom-control custom-checkbox float-right">
                                        <input type="checkbox" class="custom-control-input" id="sameAsShipping">
                                        <label class="custom-control-label" for="sameAsShipping">
                                            Same as shipping
                                        </label>
                                    </div>
                                </div>
                                <!-- Billing Form -->
                                <div class="card">
                                    <div class="card-body">
                                        <form id="billingForm">
                                            <div class="form-row mb-3">
                                                <div class="col-6">
                                                    <label for="billingFirstName">First Name</label>
                                                    <input type="text" class="form-control" id="billingFirstName" required>
                                                    <div class="invalid-feedback" id="invalidBillingFirstName"></div>
                                                </div>
                                                <div class="col-6">
                                                    <label for="billingLastName">Last Name</label>
                                                    <input type="text" class="form-control" id="billingLastName" required>
                                                    <div class="invalid-feedback" id="invalidBillingLastName"></div>
                                                </div>
                                            </div>
                                            <div class="form-row mb-3">
                                                <div class="col">
                                                    <label for="billingMobileNumber">Mobile Number</label>
                                                    <input type="text" class="form-control" id="billingMobileNumber" required>
                                                    <div class="invalid-feedback" id="invalidBillingMobileNumber"></div>
                                                </div>
                                            </div>
                                            <div class="form-row mb-3">
                                                <div class="col-6">
                                                    <label for="billingAddress1">Address (Line 1)</label>
                                                    <input type="text" class="form-control" id="billingAddress1" required>
                                                    <div class="invalid-feedback" id="invalidBillingAddress1"></div>
                                                </div>
                                                <div class="col-6">
                                                    <label for="billingAddress2">Address (Line 2)</label>
                                                    <input type="text" class="form-control" id="billingAddress2">
                                                    <div class="invalid-feedback" id="invalidBillingAddress2"></div>
                                                </div>
                                            </div>
                                            <div class="form-row mb-3">
                                                <div class="col-5">
                                                    <label for="billingTownOrCity">Town/City</label>
                                                    <input type="text" class="form-control" id="billingTownOrCity" required>
                                                    <div class="invalid-feedback" id="invalidBillingTownOrCity"></div>
                                                </div>
                                                <div class="col-7">
                                                    <label for="billingCounty">County</label>
                                                    <input type="text" class="form-control" id="billingCounty">
                                                    <div class="invalid-feedback" id="invalidBillingCounty"></div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-7">
                                                    <label for="billingCountry">Country</label>
                                                    <select class="custom-select" id="billingCountry" required>
                                                        <option selected>United Kingdom</option>
                                                    </select>
                                                </div>
                                                <div class="col-5">
                                                    <label for="billingPostCode">Post Code</label>
                                                    <input type="text" class="form-control" id="billingPostCode" required>
                                                    <div class="invalid-feedback" id="invalidBillingPostCode"></div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment-->
                <div class="tab-pane" id="navPayment" role="tabpanel">
                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <h5 class="ml-0 mb-3">Payment</h5>
                                </div>
                            </div>
                            <!-- Payment Form -->
                            <div class="card">
                                <div class="card-body checkout-payment-container">
                                    <div class="row mb-2">
                                        <div class="col text-left">
                                            <h5>Pay with Visa&reg;</h5>
                                        </div>
                                        <div class="col text-right">
                                            <img class="checkout-payment-img" src="img/visa.png">
                                        </div>
                                    </div>
                                    <form id="paymentForm">
                                        <div class="form-row mb-3">
                                            <div class="col">
                                                <label for="paymentCardNumber">Card Number</label>
                                                <input type="text" class="form-control" id="paymentCardNumber"
                                                       placeholder="4000 - 0000 - 0000 - 0002" maxlength="25" required>
                                                <div class="invalid-feedback" id="invalidPaymentCardNumber"></div>
                                            </div>
                                        </div>
                                        <div class="form-row mb-2">
                                            <div class="col-5">
                                                <label for="paymentName">Name on Card</label>
                                                <input type="text" class="form-control" id="paymentName" required>
                                                <div class="invalid-feedback" id="invalidPaymentName"></div>
                                            </div>
                                            <div class="col-4">
                                                <label for="paymentExpiry">Expiration Date</label>
                                                <input type="text" class="form-control" id="paymentExpiry"
                                                       placeholder="MM / YYYY" maxlength="9" required>
                                                <div class="invalid-feedback" id="invalidPaymentExpiry"></div>
                                            </div>
                                            <div class="col-3">
                                                <label for="paymentSecurityCode">Security Code</label>
                                                <input type="text" class="form-control" id="paymentSecurityCode"
                                                       placeholder="CVV" maxlength="4" required>
                                                <div class="invalid-feedback" id="invalidPaymentSecurityCode"></div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review & Place Order Tab -->
                <div class="tab-pane" id="navReviewAndPlaceOrder" role="tabpanel">
                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <h5 class="ml-0 mb-3">Review & Place Order</h5>
                                </div>
                            </div>
                            <!-- Shipping Information & Billing Information -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Shipping Information -->
                                        <div class="col">
                                            <h6>Shipping Address:</h6>
                                            <div id="reviewShippingAddress">
                                                <div id="reviewShippingName"></div>
                                                <div id="reviewShippingAddress1"></div>
                                                <div id="reviewShippingAddress2"></div>
                                                <div id="reviewShippingTownOrCity"></div>
                                                <div id="reviewShippingCountyAndPostCode"></div>
                                                <div id="reviewShippingCountry"></div>
                                            </div>
                                        </div>
                                        <!-- Billing Information -->
                                        <div class="col">
                                            <h6>Billing Information:</h6>
                                            <div class="mb-2" id="billingCardNumber"></div>
                                            <h6>Billing Address:</h6>
                                            <div id="reviewBillingAddress">
                                                <div id="reviewBillingSameAsShipping"></div>
                                                <div id="reviewBillingName"></div>
                                                <div id="reviewBillingAddress1"></div>
                                                <div id="reviewBillingAddress2"></div>
                                                <div id="reviewBillingTownOrCity"></div>
                                                <div id="reviewBillingCountyAndPostCode"></div>
                                                <div id="reviewBillingCountry"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Option -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6>Choose a shipping speed:</h6>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" checked="checked"
                                                       id="freeDelivery">
                                                <label class="custom-control-label" for="freeDelivery">
                                                    FREE Next Day Delivery
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Shopping Cart & Order Summary -->
                            <div class="row">
                                <div class="col padding-0">
                                    <div class="card-deck m-0">
                                        <!-- Shopping Cart -->
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="mb-3">Shopping Cart:</h6>
                                                <table class="table checkout-table">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col">Title</th>
                                                        <th scope="col">Format</th>
                                                        <th scope="col">Quantity</th>
                                                        <th scope="col">Price</th>
                                                    <tbody>
                                                    <?php

                                                    /**
                                                     * The shopping cart table is displayed if the user's shopping cart isn't empty.
                                                     */
                                                    getTable('review')

                                                    ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- Order Summary -->
                                        <div class="card">
                                            <div class="card-body">
                                                <h6>Order Summary:</h6>
                                                <?php

                                                /**
                                                 * The price breakdown column is displayed if the user's shopping cart isn't empty.
                                                 */
                                                getPrice('review')

                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Tab Button -->
                <?php

                /**
                 * The tab navigation button is displayed if the user's cart isn't empty.
                 */
                echo ($isEmptyCart) ? '' : '<button class="btn btn-success pull-right mt-3 mb-3" id="nextCheckoutTab">Checkout</button>'

                ?>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>

<!-- jQuery, Scripts, Validator, CryptoJS, Popper.js & Bootstrap JS -->
<script src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/scripts.js"></script>
<script type="text/javascript" src="libraries/validator/validator.min.js"></script>
<script type="text/javascript" src="libraries/cryptojs/core.js"></script>
<script type="text/javascript" src="libraries/cryptojs/md5.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>
</html>
