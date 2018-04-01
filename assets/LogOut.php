<?php

session_start();

/**
 * Multiple session variables are assigned false/null when a user logs out.
 */
$_SESSION['loggedIn'] = false;
$_SESSION['isAdmin'] = false;
$_SESSION['userInformation'] = null;
$_SESSION['userID'] = null;

echo json_encode(true);

exit();