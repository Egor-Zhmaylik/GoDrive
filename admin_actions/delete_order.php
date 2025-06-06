<?php
session_start();
require '../config.php';

$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
if (!$user || !$user['is_admin']) {
    die("Доступ запрещен.");
}

$order_id = (int) ($_POST['order_id'] ?? 0);

$stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
$stmt->execute([$order_id]);

header("Location: ../admin.php");
exit;
