<?php

require './vendor/autoload.php';
require './Database.php';
require '../assets/Upload.php';

$db = new Database();

/**
 * The product document is prepared.
 */
$product = [
    'title' => $_POST['title'],
    'year' => $_POST['year'],
    'dvdPrice' => $_POST['dvdPrice'],
    'bluRayPrice' => $_POST['bluRayPrice'],
    'cover' => (isset ($_FILES['cover']) ? $_FILES['cover']['name'] : $_POST['cover']),
    'dvdQuantity' => $_POST['dvdQuantity'],
    'bluRayQuantity' => $_POST['bluRayQuantity'],
    'description' => $_POST['description'],
    'actors' => $_POST['actors'],
    'directors' => $_POST['directors'],
    'format' => $_POST['format'],
    'language' => $_POST['language'],
    'subtitles' => $_POST['subtitles'],
    'region' => $_POST['region'],
    'aspectRatio' => $_POST['aspectRatio'],
    'numberOfDiscs' => $_POST['numberOfDiscs'],
    'dvdReleaseDate' => $_POST['dvdReleaseDate'],
    'runTime' => $_POST['runTime'],
    'trailer' => $_POST['trailer']
];

/**
 * Updates a product document in the products collection.
 */
if (isset($_FILES['cover'])) {
    /**
     * The product's current cover is deleted, and a new one is uploaded.
     */
    $fileName = $db->fetchProduct(['_id' => $_POST['_id']], false, 0, 0)['cover'];
    unlink('../img/products/' . $fileName);

    $hasUploaded = uploadFile($_FILES['cover'], $location = '../img/products/');

    if ($hasUploaded) {
        /**
         * The product document is updated.
         */
        $db->updateProduct($_POST['_id'], $product);
    }
}
else {
    /**
     * If single is true, a specific field in the product's document is updated. Otherwise,
     * the entire product document is updated.
     */
    $db->updateProduct($_POST['_id'], (isset($_POST['single'])) ? $_POST['product'] : $product);
}

echo json_encode(true);

exit();