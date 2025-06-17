<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'], $_POST['order_id'])) {
    header("Location: profile.php");
    exit();
}

$order_id = $_POST['order_id'];

$stmt = $pdo->prepare("SELECT o.*, c.price FROM orders o 
                       JOIN cars c ON o.car_id = c.id 
                       WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order || $order['order_type'] !== 'open' || $order['end_time']) {
    header("Location: profile.php");
    exit();
}

$now = new DateTime();
$start = new DateTime($order['start_time']);
$diffMinutes = ($now->getTimestamp() - $start->getTimestamp()) / 60;

$pricePerMinute = $order['price']; 
$totalPrice = round($pricePerMinute * $diffMinutes, 2);

$update = $pdo->prepare("UPDATE orders SET end_time = ?, total_price = ? WHERE id = ?");
$update->execute([$now->format('Y-m-d H:i:s'), $totalPrice, $order_id]);

header("Location: profile.php");
exit();
