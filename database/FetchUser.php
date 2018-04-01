<?php

require './vendor/autoload.php';
require './Database.php';

$db = new Database();

/**
 * A user document is returned.
 */
if (isset($_GET['query']) && isset($_GET['filter'])) {
    $query = $_GET['query'];
    $filter = $_GET['filter'];

    /**
     * Fetches a user document from the users collection.
     */
    $user = $db->FetchUser($query);

    /**
     * If filter is true, the value of accessing $user with key $query is returned.
     * Otherwise, the user document without the password field is returned.
     */
    if ($_GET['filter']) {
        echo json_encode($user[key($query)]);
    }
    else {
        unset($user['password']);
        echo json_encode($user);
    }
}
else {
    echo json_encode(false);
}

exit();