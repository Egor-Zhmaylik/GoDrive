<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_POST['order_id'] ?? null;
$card_number = trim($_POST['card_number'] ?? '');
$expiry = trim($_POST['expiry'] ?? '');
$cvv = trim($_POST['cvv'] ?? '');

$errors = [];

if (!preg_match('/^\d{16}$/', str_replace(' ', '', $card_number))) {
    $errors[] = "Некорректный номер карты (должно быть 16 цифр).";
}
if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry)) {
    $errors[] = "Неверный формат срока действия (MM/YY).";
}
if (!preg_match('/^\d{3}$/', $cvv)) {
    $errors[] = "CVV должен содержать 3 цифры.";
}

if (!empty($errors)) {
    $_SESSION['payment_errors'] = $errors;
    header("Location: payment.php?order_id=$order_id");
    exit();
}

$stmt = $pdo->prepare("UPDATE orders SET is_paid = 1 WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);

$_SESSION['payment_success'] = true;
header("Location: payment.php?order_id=$order_id");
exit();
