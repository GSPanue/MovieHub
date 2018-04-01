<?php

require './vendor/autoload.php';
require './Database.php';
require '../assets/Upload.php';

session_start();

$isLoggedIn = isset($_SESSION['userID']);
$id = ($isLoggedIn) ? $_SESSION['userID'] : $_SESSION['guestID'];

$db = new Database();

$cart = $db->fetchCart(['userID' => $id], false);

/**
 * The order document is prepared.
 */
$order = [
    'userID' => (isset($_SESSION['userID']) ? $_SESSION['userID'] : null),
    'dateCreated' => new MongoDB\BSON\UTCDateTime(),
    'orderNumber' => $db->countDocuments('orders') + 1,
    'status' => 'Pending',
    'shipping' => $_POST['shipping'],
    'billing' => $_POST['billing'],
    'delivery' => $_POST['delivery'],
    'package' => getPackage($cart),
    'summary' => getPrice($cart)
];


/**
 * getPackage: Returns an array containing the items a user has purchased. The ID for each item is
 * removed.
 */
function getPackage($cart) {
    $array = [];

    for ($i = 0; $i < sizeof($cart['items']); $i++) {
        $cartItem = $cart['items'][$i];
        unset($cartItem['_id']);

        array_push($array, $cartItem);
    }

    return $array;
}

/**
 * getPrice: Returns a summary of the price.
 */
function getPrice($cart) {
    $cart = $cart['items'];

    $totalCost = 0;

    /**
     * Sums the cost of each product in the user's cart.
     */
    for ($i = 0; $i < sizeof($cart); $i++) {
        $quantity = ['dvdQuantity' => (int)$cart[$i]['dvdQuantity'], 'bluRayQuantity' => (int)$cart[$i]['bluRayQuantity']];

        foreach ($quantity as $key => $value) {
            $isDVD = ($key) === 'dvdQuantity';

            if ($value > 0) {
                $totalCost += ($value * (float)$cart[$i][($isDVD) ? 'dvdPrice' : 'bluRayPrice']);
            }
        }
    }

    /**
     * The total cost summary is calculated.
     */
    $priceBreakdown = ['subTotal' => bcdiv($totalCost, 1, 2),
        'vat' => bcdiv((0.20 * $totalCost), 1, 2),
        'postAndPackaging' => 'Free',
        'grandTotal' => bcdiv(($totalCost + (0.20 * $totalCost)), 1, 2)
    ];

    return $priceBreakdown;
}

/**
 * Adds an order to the orders collection.
 */
$db->addOrder($order);

/**
 * Empties the user's cart.
 */
$db->updateCart(['userID' => $id], [], 0, true);

/**
 * The order number is returned.
 */
echo json_encode(['orderNumber' => $order['orderNumber']]);

exit();