<?php
session_start();
require 'config.php';

// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Неверный номер телефона или пароль.";
        header("Location: index.php#login");
        exit();
    }
}
?>
