<?php

require '../database/vendor/autoload.php';
require '../database/Database.php';

$db = new Database();

/**
 * A document containing the user's registration input is added to the users collection.
 */
$db->addUser(json_decode($_POST['userInput'], true), 'user');

echo json_encode(true);

exit();