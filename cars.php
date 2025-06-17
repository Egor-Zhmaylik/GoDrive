<?php
session_start();
require "config.php"; 

$query = $pdo->query("SELECT * FROM cars");
$cars = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Выбор автомобиля</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick-theme.css"/>
</head>
<body>

<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="index.php">Главная</a></li>
            <li><a href="#contacts">Контакты</a></li>
        </ul>
        <div class="auth-buttons">
            <?php if (isset($_SESSION["user_id"])): ?>
                <a href="profile.php" class="btn btn-primary">Профиль</a>
                <a href="logout.php" class="btn">Выход</a>
            <?php else: ?>
                <button id="login-button" class="btn btn-primary">Войти</button>
                <button id="register-button" class="btn">Регистрация</button>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main>
    <section class="cars-page">
        <div class="car-list-container">
            <div class="car-list">
                <?php foreach ($cars as $car): ?>
                    <div class="car">
                        <img src="carphotoes/<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['title']) ?>">
                        <h3><?= htmlspecialchars($car['title']); ?></h3>
                        <p>Тариф: <?= number_format($car['price'], 2, ',', ' '); ?> BYN/мин</p>
                        <a href="order.php?id=<?= $car['id'] ?>"><button class="car-btn">Оформить</button></a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<script>
$(document).ready(function(){
    $('.car-slider').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
        dots: true,
        arrows: true,
        responsive: [
            {
                breakpoint: 1024,
                settings: { slidesToShow: 2 }
            },
            {
                breakpoint: 600,
                settings: { slidesToShow: 1 }
            }
        ]
    });

    $(".rent-btn").click(function () {
        let carId = $(this).data("car-id");
        <?php if (!isset($_SESSION["user_id"])): ?>
            alert("Пожалуйста, авторизуйтесь!");
        <?php else: ?>
            window.location.href = "profile.php?car_id=" + carId;
        <?php endif; ?>
    });
});
</script>

</body>
</html>
