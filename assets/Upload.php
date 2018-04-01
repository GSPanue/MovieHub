<?php

/**
 * uploadFile: Stores a file within a given location.
 */
function uploadFile($file, $location) {
    $fileName = $file['name'];
    $tempName = $file['tmp_name'];

    if (move_uploaded_file($tempName, $location . $fileName)) {
        return true;
    }
    else {
        return false;
    }
}