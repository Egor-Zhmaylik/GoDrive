<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оплата прошла</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #e0ffe0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .success-box {
            background: #fff;
            padding: 30px;
            border: 2px solid #38a169;
            border-radius: 10px;
            text-align: center;
        }
        .success-box h2 {
            color: #38a169;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            color: orange;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="success-box">
        <h2>Оплата прошла успешно!</h2>
        <p>Спасибо за ваш заказ 🎉</p>
        <a href="profile.php">Вернуться в профиль</a>
    </div>
</body>
</html>
