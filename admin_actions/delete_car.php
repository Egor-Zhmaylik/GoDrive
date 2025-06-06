<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['car_id'])) {
    $car_id = (int)$_POST['car_id'];

    // Получаем имя изображения из БД
    $stmt = $pdo->prepare("SELECT image FROM cars WHERE id = ?");
    $stmt->execute([$car_id]);
    $car = $stmt->fetch();

    if ($car) {
        $imagePath = '../carphotoes/' . $car['image'];

        // Обновляем связанные заказы — ставим car_id = NULL
        $updateOrders = $pdo->prepare("UPDATE orders SET car_id = NULL WHERE car_id = ?");
        $updateOrders->execute([$car_id]);

        // Удаляем запись из таблицы cars
        $deleteStmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
        $deleteStmt->execute([$car_id]);

        // Удаляем изображение, если оно существует
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}

header('Location: ../admin.php');
exit();
