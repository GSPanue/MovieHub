<?php

require './vendor/autoload.php';
require './Database.php';
require '../assets/Upload.php';

$db = new Database();

/**
 * Updates an order document in the orders collection.
 */
if (isset($_POST['query']) && isset($_POST['update'])) {
    $db->updateOrder($_POST['query'], $_POST['update']);
}

echo json_encode(true);

exit();