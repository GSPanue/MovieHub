<?php

require './vendor/autoload.php';
require './Database.php';

session_start();

$db = new Database();

/**
 * The user's ID is obtained.
 */
$id = ((isset($_SESSION['userID'])) ? $_SESSION['userID'] : $_SESSION['guestID']);

/**
 * The user's cart is returned.
 */
echo json_encode($cart = $db->fetchCart(['userID' => $id], false)['items']);

exit();