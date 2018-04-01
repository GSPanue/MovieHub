<?php

require './vendor/autoload.php';
require './Database.php';

$db = new Database();

/**
 * A single product document or multiple order documents are returned.
 */
if (isset($_GET['query']) || isset($_GET['sort'])) {
    if ($_GET['sort']) {
        /**
         * Returns multiple product documents sorted in ascending or descending order.
         */
        echo json_encode($db->fetchProduct([], $_GET['sort'], (int)$_GET['order'], (int)$_GET['limit']));
    }
    else {
        /**
         * Returns a single product document.
         */
        echo json_encode($db->fetchProduct($_GET['query'], false, false, 0));
    }
}

exit();