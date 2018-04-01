<?php

require '../database/vendor/autoload.php';
require '../database/Database.php';

session_start();

$db = new Database();

/**
 * retrieveCart: Returns a user's cart if it exists.
 */
function retrieveCart() {
    global $db;

    /**
     * If the user is not logged in, the guest session variable is assigned a MongoDB variable.
     */
    $isLoggedIn = isset($_SESSION['userID']);
    $id = ($isLoggedIn) ? $_SESSION['userID'] : $_SESSION['guestID'];

    $cart = null;

    /**
     * The cart for the user is fetched. If the cart is not found, a new cart is created with the user's id.
     */
    ($db->fetchCart(['userID' => $id], false) === null) ? $db->addCart([
        'userID' => $id,
        'dateCreated' => new MongoDB\BSON\UTCDateTime(),
        'type' => ($isLoggedIn) ? 'user' : 'guest',
        'items' => []
    ]) : $cart = $db->fetchCart(['userID' => $id], false);

    /**
     * The cart
     */
    return json_encode($cart['items']);
}

/**
 * A pre-existing cart is returned.
 */
echo retrieveCart();

exit();