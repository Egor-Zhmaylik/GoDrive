<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Неверный метод запроса.");
}

$id = (int)$_POST['id'];
$title = $_POST['title'];
$price = $_POST['price'];
$daily_price = $_POST['daily_price'];

// Обновление фото, если загружено
if (!empty($_FILES['photo']['name'])) {
    $photoName = uniqid() . '_' . basename($_FILES['photo']['name']);
    $photoPath = '../carphotoes/' . $photoName;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
        $stmt = $pdo->prepare("UPDATE cars SET title = ?, price = ?, daily_price = ?, image = ? WHERE id = ?");
        $stmt->execute([$title, $price, $daily_price, $photoName, $id]);
    } else {
        die("Ошибка загрузки изображения.");
    }
} else {
    $stmt = $pdo->prepare("UPDATE cars SET title = ?, price = ?, daily_price = ? WHERE id = ?");
    $stmt->execute([$title, $price, $daily_price, $id]);
}

header("Location: ../admin.php");
exit;
