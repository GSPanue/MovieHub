<?php

session_start();

require '../database/vendor/autoload.php';
require '../database/Database.php';

$db = new Database();

/**
 * setUserInformation: Assigns an array of user information to the userInformation session variable.
 */
function setUserInformation($user) {
    $_SESSION['userInformation'] = [
        'firstName' => $user['firstName'],
        'lastName' => $user['lastName'],
        'emailAddress' => $user['emailAddress'],
        'mobileNumber' => $user['mobileNumber'],
        'dateOfBirthDay' => explode("-", $user['dateOfBirth'])[0],
        'dateOfBirthMonth' => explode("-", $user['dateOfBirth'])[1],
        'dateOfBirthYear' => explode("-", $user['dateOfBirth'])[2],
        'address1' => $user['address1'],
        'address2' => $user['address2'],
        'townOrCity' => $user['townOrCity'],
        'county' => $user['county'],
        'country' => $user['country'],
        'postCode' => $user['postCode']
    ];
}

/**
 * setUserID: Assigns the users account ID to the userID session variable.
 */
function setUserID($user) {
    $_SESSION['userID'] = $user['_id'];
}

/**
 * Verifies that the submitted e-mail address and password are valid.
 */
if (isset($_POST['emailAddress']) && isset($_POST['password'])) {
    $emailAddress = $_POST['emailAddress'];
    $password = $_POST['password'];

    /**
     * Fetches an account that matches the submitted e-mail address.
     */
    $user = $db->fetchUser(['emailAddress' => $emailAddress]);

    $isValid = false;

    /**
     * If the submitted e-mail address and password match the fetched account user information
     * is assigned to multiple session variables and true is returned to indicate that the submitted information
     * is valid.
     */
    if ($emailAddress == $user['emailAddress'] && $password == $user['password']) {
        $_SESSION['loggedIn'] = true;

        setUserInformation($user);
        setUserID($user);

        $_SESSION['isAdmin'] = ($user['role'] === 'admin');

        $isValid = true;
    }

    echo json_encode($isValid);
}