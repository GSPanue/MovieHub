<?php

session_start();

require './vendor/autoload.php';
require './Database.php';

$db = new Database();

/**
 * A single order document or multiple order documents are returned.
 */
if (isset($_GET['query']) || isset($_GET['sort'])) {
    if ($_GET['sort'] && $_SESSION['isAdmin']) {
        /**
         * Returns multiple order documents sorted in ascending or descending order.
         */
        echo json_encode($db->fetchOrder([], $_GET['sort'], (int)$_GET['order'], (int)$_GET['limit']));
    }
    else {
        /**
         * Returns a single order document.
         */
        echo json_encode($db->fetchOrder($_GET['query'], false, false, 0));
    }
}

exit();