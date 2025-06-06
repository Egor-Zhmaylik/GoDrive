<?php
require '../config.php';

if (!isset($_GET['id'])) {
    die("ID автомобиля не указан.");
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
    die("Автомобиль не найден.");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать автомобиль</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 20px;
        }
        .form-container {
            background: white;
            padding: 20px;
            max-width: 500px;
            margin: auto;
            border-radius: 8px;
        }
        input, label, button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            font-size: 16px;
        }
        button {
            background-color: #ED872D;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: darkorange;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>✏️ Редактировать автомобиль</h2>
    <form action="update_car.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $car['id'] ?>">
        <label>Название:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($car['title']) ?>" required>

        <label>Цена за минуту:</label>
        <input type="number" step="0.01" name="price" value="<?= $car['price'] ?>" required>

        <label>Цена за день:</label>
        <input type="number" step="0.01" name="daily_price" value="<?= $car['daily_price'] ?>" required>

        <label>Изменить фото (необязательно):</label>
        <input type="file" name="photo" accept="image/*">

        <button type="submit">💾 Сохранить изменения</button>
    </form>
    <a href="../admin.php" style="margin-left: 20px; padding: 7px 10px; background:#ED872D; color:white; text-decoration:none; border-radius:5px;">← Назад</a>
</div>

</body>
</html>
