<?php

require './vendor/autoload.php';
require './Database.php';
require '../assets/Upload.php';

$db = new Database();

/**
 * The product document is prepared.
 */
$product = [
    'dateCreated' => new MongoDB\BSON\UTCDateTime(),
    'title' => $_POST['title'],
    'year' => $_POST['year'],
    'dvdPrice' => $_POST['dvdPrice'],
    'bluRayPrice' => $_POST['bluRayPrice'],
    'cover' => $_FILES['cover']['name'],
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
 * Adds a product to the products collection.
 */
$db->addProduct($product);

/**
 * The cover for a product is uploaded.
 */
$hasUploaded = uploadFile($_FILES['cover'], $location = '../img/products/');

echo json_encode($hasUploaded);