<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $daily_price = $_POST['daily_price'];

    // Проверка и загрузка фото
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoTmp = $_FILES['photo']['tmp_name'];
        $photoName = time() . '_' . basename($_FILES['photo']['name']); // уникальное имя
        $uploadDir = '../carphotoes/';
        $photoPath = $uploadDir . $photoName;

        if (move_uploaded_file($photoTmp, $photoPath)) {
            // Успешная загрузка, сохраняем в БД
            $stmt = $pdo->prepare("INSERT INTO cars (title, image, price, daily_price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $photoName, $price, $daily_price]);
            header("Location: ../admin.php");
            exit();
        } else {
            echo "Ошибка загрузки файла.";
        }
    } else {
        echo "Файл не выбран или произошла ошибка при загрузке.";
    }
} else {
    echo "Недопустимый метод запроса.";
}
