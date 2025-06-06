<?php
session_start();
$response = array();

// Проверяем, есть ли пользователь в сессии
if (isset($_SESSION['user'])) {
    $response['isAuthenticated'] = true;
} else {
    $response['isAuthenticated'] = false;
}

echo json_encode($response); // Возвращаем статус в формате JSON
?>
