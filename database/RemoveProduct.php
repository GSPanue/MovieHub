<?php

require './vendor/autoload.php';
require './Database.php';

$db = new Database();

/**
 * Removes a product document from the products collection.
 */
if (isset($_POST['query'])) {
    /**
     * The product's cover is deleted.
     */
    $fileName = $db->fetchProduct(['_id' => $_POST['query']['_id']], false, 0, 0)['cover'];
    unlink('../img/products/' . $fileName);

    /**
     * The product document is removed from the products collection.
     */
    $db->removeProduct($_POST['query']);

    echo json_encode(true);
}

exit();