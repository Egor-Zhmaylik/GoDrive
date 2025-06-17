<?php
session_start();
$response = array();

if (isset($_SESSION['user'])) {
    $response['isAuthenticated'] = true;
} else {
    $response['isAuthenticated'] = false;
}

echo json_encode($response); 
?>
