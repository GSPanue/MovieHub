<?php

session_start();

require './vendor/autoload.php';
require './Database.php';

$db = new Database();

/**
 * updateSession: Assigns the userInformation session variable with new values.
 */
function updateSession() {
    foreach ($_POST['update'] as $key => $value) {
        $_SESSION['userInformation'][$key] = $_POST['update'][$key];
    }
}

/**
 * Updates the user's account information.
 */
if (isset($_POST['update']) && isset($_SESSION['userID'])) {
    /**
     * The user's password is updated if updatePassword is true.
     */
    if ($_POST['updatePassword']) {
        /**
         * The user's current password is fetched.
         */
        $currentPassword = $db->fetchUser(['_id' => $_SESSION['userID']])['password'];

        /**
         * If the user's current password matches the submitted password, the user's
         * account password is updated.
         */
        if ($_POST['update']['currentPassword'] == $currentPassword) {
            $db->updateUser($_SESSION['userID'], ['password' => $_POST['update']['newPassword']]);

            echo json_encode(true);
        }
        else {
            echo json_encode(false);
        }
    }
    else {
        /**
         * The user's account information is updated.
         */
        $db->updateUser($_SESSION['userID'], $_POST['update']);

        /**
         * The userInformation session variable is updated with new values.
         */
        updateSession();

        echo json_encode(true);
    }
}

exit();