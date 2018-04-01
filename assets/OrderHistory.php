<?php

require '../database/vendor/autoload.php';
require '../database/Database.php';

session_start();

$db = new Database();

$orderID = $_GET['orderID'];

/**
 * Fetches multiple orders or a single order if the orderID session variable is set.
 */
$data = ($orderID !== null) ? $db->fetchOrder(['_id' => $_GET['orderID']], false, 0, 0)
    : $db->fetchOrder(['userID' => $_SESSION['userID']], true, -1, 0);

/**
 * getOrders: Returns a list of orders.
 */
function getOrders() {
    global $data;

    $html = '';

    /**
     * Each order is appended to $html.
     */
    for ($i = 0; $i < sizeof($data); $i++) {
        $order = $data[$i];

        $dateCreated = $order['dateCreated'];
        $dateCreated = new DateTime($dateCreated->toDateTime()->format('Y-m-d H:i:s'));

        $html .= '<tr>
                    <td class="order-row">' . $dateCreated->format('d/m/Y') . '</td>
                    <td class="order-row">' . $order['orderNumber'] . '</td>
                    <td class="order-row">' . '£' . $order['summary']['grandTotal'] . '</td>
                    <td class="order-row">' . $order['status'] . '</td>
                    <td align="middle" class="order-row">
                        <button class="btn btn-outline-dark btn-sm view-order" id="' . $order['_id'] . '" onclick="viewOrderHistory(this)">
                            View
                        </button>
                    </td>
                  </tr>';
    }

    return $html;
}

/**
 * Returns html for the user's order history.
 */
function getContent() {
    global $data;

    $content = '<div class="row mb-3 align-items-center">
        <div class="col">
            <h5 class="m-0">' . ((isset($_GET['orderID'])) ? 'Order History (#' . $data['orderNumber'] . ')' : 'Order History') . '</h5>
        </div>' . ((isset($_GET['orderID'])) ? '<div class="col">
            <button class="btn btn-outline-danger btn-sm pull-right" onclick="restoreOrderHistory(this)">
                <i class="fa fa-undo"></i> Go Back
            </button>
        </div>' : '') .
    '</div>';

    /**
     * If orderID is set, the html for a single order is appended to $content.
     */
    if (isset($_GET['orderID'])) {
        $dateCreated = $data['dateCreated'];
        $dateCreated = new DateTime($dateCreated->toDateTime()->format('Y-m-d H:i:s'));

        /**
         * getShippingOrBillingInformation: Returns html for the shipping/billing information.
         */
        function getShippingOrBillingInformation($order, $type) {
            $html = '';

            if ($type === 'billing' && json_decode($order['billing']['sameAsShipping'])) {
                return '<div>Same as shipping address</div>';
            }
            else {
                $array = [
                    $order[$type]['firstName'] . ' ' . $order[$type]['lastName'],
                    $order[$type]['address1'] . (($order[$type]['address2'] !== '') ? (', ' . $order[$type]['address2']) : ''),
                    $order[$type]['townOrCity'],
                    (($order[$type]['county'] !== '') ? $order[$type]['county'] . ', ' : '') . $order[$type]['postCode'],
                    $order[$type]['country']
                ];

                for ($i = 0; $i < sizeof($array); $i++) {
                    $html .= '<div>' . $array[$i] . '</div>';
                }
            }

            return $html;
        }

        /**
         * getPackageInformation: Returns html for the order package.
         */
        function getPackageInformation($order) {
            $package = $order['package'];
            $html = '';

            for ($i = 0; $i < sizeof($package); $i++) {
                $productName = $package[$i]['title'] . ' (' . $package[$i]['year'] . ')';
                $quantity = ['DVD' => $package[$i]['dvdQuantity'], 'Blu-ray' => $package[$i]['bluRayQuantity']];
                $price = ['DVD' => $package[$i]['dvdPrice'], 'Blu-ray' => $package[$i]['bluRayPrice']];

                foreach ($quantity as $key => $value) {
                    if ((int)$value > 0) {
                        $html .= '<li>' . $value . ' x ' . $productName . ' [Format: ' . $key . '] ' . '[Price: £' . $price[$key] . ']' . '</li>';
                    }
                }


            }

            return $html;
        }

        $content .= '<div class="row m-0 mb-3">
    <!-- Date of Purchase & Status -->
    <div class="card w-100 mb-2">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h6>Date of Purchase:</h6>
                    <div>' . $dateCreated->format('d/m/Y') . '</div>
                </div>
                <div class="col">
                    <h6>Status:</h6>
                    <div>' . $data['status'] . '</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipping & Billing Information-->
    <div class="card w-100 mb-2">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h6>Shipping Address:</h6>' .
            getShippingOrBillingInformation($data, 'shipping') .
            '</div>
                <div class="col">
                    <h6>Billing Information:</h6>
                    <div class="mb-2" id="billingCardNumber">Card ending in ' . $data['billing']['cardNumber'] . '</div>
                    <h6>Billing Address:</h6>' .
            getShippingOrBillingInformation($data, 'billing') .
            '</div>
            </div>
        </div>
    </div>

    <!-- Shipping Speed -->
    <div class="card w-100 mb-2">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h6>Shipping Speed:</h6>
                    <div>' . $data['delivery'] . '</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Package -->
    <div class="card w-100 mb-2">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h6>Package:</h6>
                    <ul class="history-list">' .
                        getPackageInformation($data) .
                    '</ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="card w-100">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h6>Order Summary:</h6>
                    <div class="row">
                        <div class="col">
                            <div>Subtotal (excl. VAT):</div>
                        </div>
                        <div class="col text-right">
                            <div>£' . $data['summary']['subTotal'] . '</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div>VAT (20%):</div>
                        </div>
                        <div class="col text-right">
                            <div>£' . $data['summary']['vat'] . '</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div>Post & Packaging:</div>
                        </div>
                        <div class="col text-right">
                            <div>' . $data['summary']['postAndPackaging'] . '</div>
                        </div>
                    </div>
                    <div class="row mt-2 history-total">
                        <div class="col">
                            <div>Grand Total:</div>
                        </div>
                        <div class="col text-right">
                            <div>£' . $data['summary']['grandTotal'] . '</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
    }
    /**
     * If orderID is not set, html for the user's order history table is appended to $content.
     */
    else {
        $content .= '<table class="table order-table">
                        <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Order #</th>
                            <th scope="col">Total</th>
                            <th scope="col">Status</th>
                            <th scope="col">Details</th>
                        <tbody>' . getOrders() . '</tbody></table>';
    }

    return $content;
}

/**
 * The html for a user's order history is returned.
 */
echo getContent();