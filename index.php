<?php
session_start();
require 'config.php'; // Подключаем базу данных

$isAuthenticated = false;
$isAdmin = false;

if (isset($_SESSION['user_id'])) {
    $isAuthenticated = true;

    // Проверка, является ли пользователь админом
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user && $user['is_admin']) {
        $isAdmin = true;
    }
}

// Получаем 7 случайных авто из БД
$stmt = $pdo->query("SELECT * FROM cars ORDER BY RAND() LIMIT 8");
$cars = $stmt->fetchAll();
?>

<script>
    var isLoggedIn = <?php echo $isAuthenticated ? 'true' : 'false'; ?>;
</script>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoDrive - Каршеринг</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <ul class="nav-links">
                <li><a href="#" onclick="scrollToTop()">Главная</a></li>
                <li><a href="#about">О нас</a></li>
                <li><a href="#how-it-works">Как это работает</a></li>
                <li><a href="#cars">Автомобили</a></li>
                <li><a href="#contacts">Контакты</a></li>
                
            </ul>
            <div class="auth-buttons">
                <?php if ($isAuthenticated): ?>
                    <a href="profile.php" class="btn">Профиль</a>
                    <a href="logout.php" class="btn btn-primary">Выход</a>
                    <?php if ($isAdmin): ?>
                    <a href="admin.php" class="btn">Админ-панель</a>
                <?php endif; ?>
                <?php else: ?>
                    <button id="login-button" class="btn">Войти</button>
                    <button id="register-button" class="btn btn-primary">Регистрация</button>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>

    <section id="home" class="hero">
        <div class="hero-text">
            <h1>GoDrive</h1>
            <p>Удобный, быстрый и доступный<br>способ передвижения по городу!</p>
        </div>
    </section>

    <section id="welcome" class="zone welcome">
        <div class="text">
            <h2>Добро пожаловать в GoDrive</h2>
            <p>Выбирайте автомобиль, бронируйте его <br>и пользуйтесь по выгодным тарифам!</p>
        </div>
    </section>

    <section id="about" class="about">
        <div class="text">
            <h2>О нас</h2>
            <p>GoDrive — это современный сервис каршеринга, <br>который делает аренду автомобилей удобной и доступной.</p>
        </div>
    </section>

    <section id="how-it-works" class="services">
        <div class="text">
            <h2>Как это работает</h2>
            <ul>
                <li>Регистрируйтесь на сайте</li>
                <li>Выбирайте подходящий автомобиль</li>
                <li>Оплачивайте и начните поездку</li>
                <li>Оставьте автомобиль в разрешенной зоне</li>
            </ul>
        </div>
    </section>

    <section id="cars" class="cars-main">
        <div class="car-list-container">
            <div class="car-list">
                <?php foreach ($cars as $index => $car): ?>
                    <?php if ($index === 7): ?>
                        <!-- 7-я карточка — кнопка -->
                        <div class="car more-options">
                        <button class="more-btn">Посмотреть другие варианты</button>
                        </div>
                    <?php else: ?>
                        <div class="car">
                            <img src="carphotoes/<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['title']) ?>">
                            <h3><?= htmlspecialchars($car['title']) ?></h3>
                            <p>Тариф: <?= number_format($car['price'], 2) ?> BYN/мин</p>
                            <button class="car-btn" onclick="handleOrder(<?= $car['id'] ?>)">Оформить</button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <section id="contacts" class="contacts">
        <div class="contacts-container">
            <div class="contact-info">
                <h2>Контакты</h2>
                <p>Email: godrive.site@gmail.com</p>
                <p>Телефон: 800-2345-6789</p>
                <p>Адрес: Гродно, ул. Центральная, 10</p>
            </div>
            <div class="map-section">
                <h3>Также можете выбрать авто, который ближе всего к вам.</h3>
                <div class="map-container">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Подключение Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="map.js"></script>

    <footer>
        <p>&copy; 2025 GoDrive. Все права защищены.</p>
    </footer>

    <script>
        // Плавная прокрутка к секциям
        document.querySelectorAll('nav ul li a').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();

                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);

                window.scrollTo({
                    top: targetSection.offsetTop,
                    behavior: 'smooth'
                });
            });
        });
    </script>

    <!-- Модальное окно -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="login-form">
                <h2>Вход</h2>
                <form id="login-form" action="login.php" method="post">
                    <input type="text" placeholder="Номер телефона" name="phone" id="login-phone" required>
                    <input type="password" placeholder="Пароль" name="password" id="login-password" required>
                    <button type="submit">Войти</button>
                </form>
                <p>Нет аккаунта? <a href="#" id="show-register">Регистрация</a></p>
                <?php if (isset($_SESSION['login_error'])): ?>
                    <p style="color:red;"><?php echo $_SESSION['login_error']; ?></p>
                    <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>
            </div>

            <div id="register-form" style="display: none;">
                <h2>Регистрация</h2>
                <form id="registration-form" action="register.php" method="post">
                    <input type="text" name="lastname" id="lastname" placeholder="Фамилия" required>
                    <input type="text" name="firstname" id="firstname" placeholder="Имя" required>
                    <input type="text" name="middlename" id="middlename" placeholder="Отчество" required>
                    <input type="text" name="phone" id="phone" placeholder="Телефон" required>
                    <input type="text" name="passport_number" id="passport_number" placeholder="Номер паспорта" required>
                    <input type="text" name="driver_license" id="driver_license" placeholder="Номер водительского удостоверения" required>
                    <input type="email" name="email" id="email" placeholder="Email" required>
                    <input type="password" name="password" id="password" placeholder="Пароль" required>
                    <button type="submit">Зарегистрироваться</button>
                </form>
                <p>Уже есть аккаунт? <a href="#" id="show-login">Войти</a></p>
                <?php if (isset($_SESSION['register_errors'])): ?>
                    <div class="error-message">
                        <?php foreach ($_SESSION['register_errors'] as $error): ?>
                            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                    <?php unset($_SESSION['register_errors']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['register_success'])): ?>
                    <p style="color:green;"><?php echo $_SESSION['register_success']; ?></p>
                    <?php unset($_SESSION['register_success']); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    function handleOrder(carId) {
        const isAuthenticated = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

        if (isAuthenticated) {
            window.location.href = 'order.php?id=' + carId;
        } else {
            // Открыть модальное окно авторизации
            const modal = document.getElementById('modal');
            modal.style.display = 'flex';
        }
    }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (window.location.hash === "#register") {
                document.getElementById("modal").style.display = "flex";
                document.getElementById("login-form").style.display = "none";
                document.getElementById("register-form").style.display = "block";
            } else if (window.location.hash === "#login") {
                document.getElementById("modal").style.display = "flex";
                document.getElementById("login-form").style.display = "block";
                document.getElementById("register-form").style.display = "none";
            }
        });
    </script>

    <script src="script.js"></script>
    <script src="validation.js"></script>
    

</body>
</html>
