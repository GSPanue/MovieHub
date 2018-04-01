<?php

session_start();

/**
 * Returns a requested session variable.
 */
echo json_encode((isset($_GET['request']) ? $_SESSION[$_GET['request']] : $_SESSION));

exit();