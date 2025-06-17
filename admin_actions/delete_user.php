<?php
session_start();
require '../config.php';

$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
if (!$user || !$user['is_admin']) {
    die("Доступ запрещен.");
}

$user_id = (int) ($_POST['user_id'] ?? 0);

if ($user_id !== $_SESSION['user_id']) { 
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
}

header("Location: ../admin.php");
exit;
