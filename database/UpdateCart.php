<?php

session_start();

require './vendor/autoload.php';
require './Database.php';

$db = new Database();

/**
 * The user's ID is obtained.
 */
$id = ['userID' => ((isset($_SESSION['userID'])) ? $_SESSION['userID'] : $_SESSION['guestID'])];

/**
 * isInCart: Checks if a product in the user's cart exists, and can return the
 * index of the product if $getIndex is true.
 */
function isInCart($product, $cart, $getIndex) {
    $productID = $product['_id'];
    $items = $cart['items'];

    $i = 0;

    foreach ($items as $value) {
        if ((string)$value['_id'] === (string)$productID) {
            return (($getIndex) ? $i : true);
        }

        $i++;
    }

    return false;
}

/**
 * prepareCartItem: Returns a cart item.
 */
function prepareCartItem($product, $format, $quantity) {
    $isDvd = ($format === 'dvd');

    return [
        '_id' => $product['_id'],
        'title' => $product['title'],
        'year' => $product['year'],
        'dvdPrice' => $product['dvdPrice'],
        'bluRayPrice' => $product['bluRayPrice'],
        'dvdQuantity' => ($isDvd) ? (string)$quantity : '0',
        'bluRayQuantity' => (!$isDvd) ? (string)$quantity : '0',
    ];
}

/**
 * changeQuantity: Changes the DVD quantity or Blu-ray quantity of a product.
 */
function changeQuantity($cart, $index, $quantity, $action) {
    $key = ($_POST['product']['format'] === 'dvd') ? 'dvdQuantity' : 'bluRayQuantity';
    $newQuantity = $cart['items'][$index][$key] += (($action === 'add') ? $quantity : -$quantity);

    $cart['items'][$index][$key] = (string)$newQuantity;

    return $cart;
}

/**
 * restockProduct: Restocks a product when it has been deleted from the user's cart.
 */
function restockProduct($product, $quantity) {
    global $db;
    $key = ($_POST['product']['format'] === 'dvd') ? 'dvdQuantity' : 'bluRayQuantity';

    $product[$key] = (string)((int)$product[$key] += $quantity);

    $db->updateProduct($product['_id'], $product);
}

/**
 * Updates a user's cart document in the carts collection.
 */
if (isset($_POST['product'])) {
    $productID = ['_id' => new \MongoDB\BSON\ObjectID($_POST['product']['_id'])];

    /**
     * The product is fetched from the products collection.
     */
    $product = $db->fetchProduct($productID, false, false, 0);

    if ($product !== null) {
        /**
         * The quantity of the item in the user's cart and action is assigned.
         */
        $quantity = $_POST['product']['quantity'];
        $action = $_POST['action'];

        /**
         * The user's cart is fetched from the carts collection.
         */
        $cart = $db->fetchCart($id, false);

        /**
         * If the product is found in the cart, the cart is updated. Otherwise,
         * the product is added to the user's cart.
         */
        if (isInCart($product, $cart, false)) {
            $indexOfProduct = isInCart($product, $cart, true);

            /**
             * The quantity of the item in the user's cart is changed.
             */
            $cart = changeQuantity($cart, $indexOfProduct, $quantity, $action);

            /**
             * If $action is remove, the product is restocked. The item is then removed from the user's cart.
             */
            ($action === 'remove') ? restockProduct($product, $quantity) : false;
            $db->updateCart($id, ['_id' => $cart['items'][$indexOfProduct]['_id']], 0, false);

            /**
             * If the DVD quantity or Blu-ray quantity of the item is greater than zero, it is added back to the user's cart.
             */
            if ((int)$cart['items'][$indexOfProduct]['dvdQuantity'] > 0 || (int)$cart['items'][$indexOfProduct]['bluRayQuantity'] > 0) {
                $db->updateCart($id, $cart['items'][$indexOfProduct], 1, false);
            }

            echo json_encode(($action === 'remove') ? [$db->fetchCart($id, false)['items'], $db->fetchProduct([], true, -1, 0)] : true);
        }
        else {
            /**
             * The item is added to the user's cart if it doesn't exist in the user's cart already.
             */
            $cartItem = prepareCartItem($product, $_POST['product']['format'], $quantity);

            $db->updateCart($id, $cartItem, 1, false);

            echo json_encode(true);
        }
    }
}

exit();