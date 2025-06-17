<?php
session_start();
require 'config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT lastname, firstname, middlename, phone, passport_number, driver_license, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Ошибка: Пользователь не найден.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="index.php">Главная</a></li>
            <li><a href="cars.php">Выбор авто</a></li>
            <li><a href="map_public.php">Посмотреть карту автомобилей</a></li>
            <li><a href="profile.php" class="active">Профиль</a></li>
        </ul>
    </nav>
</header>

<main class="profile-container">
    <h2>Добро пожаловать, <?php echo htmlspecialchars($user['firstname']); ?>!</h2>

    <div class="profile-info">
        <p><strong>Фамилия:</strong> <?php echo htmlspecialchars($user['lastname']); ?></p>
        <p><strong>Имя:</strong> <?php echo htmlspecialchars($user['firstname']); ?></p>
        <p><strong>Отчество:</strong> <?php echo htmlspecialchars($user['middlename']); ?></p>
        <p><strong>Телефон:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Номер паспорта:</strong> <?php echo htmlspecialchars($user['passport_number']); ?></p>
        <p><strong>Водительское удостоверение:</strong> <?php echo htmlspecialchars($user['driver_license']); ?></p>
    </div>

    <a href="logout.php" class="btn">Выйти</a>
    <?php
        $stmt = $pdo->prepare("SELECT o.*, c.title, c.image FROM orders o 
                            JOIN cars c ON o.car_id = c.id 
                            WHERE o.user_id = ? ORDER BY o.created_at DESC");
        $stmt->execute([$_SESSION["user_id"]]);
        $orders = $stmt->fetchAll();
        ?>

        <h3>Ваши заказы</h3>

        <?php if (count($orders) === 0): ?>
            <p>У вас пока нет заказов.</p>
        <?php else: ?>
            <div class="order-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <img src="carphotoes/<?= htmlspecialchars($order['image']) ?>" alt="<?= htmlspecialchars($car['title']) ?>">
                        <div>
                            <h4><?= htmlspecialchars($order['title']) ?></h4>
                            <p><strong>Тип аренды:</strong>
                                <?php
                                switch ($order['order_type']) {
                                    case 'open':
                                        echo 'До завершения';
                                        break;
                                    case 'scheduled':
                                        echo 'По времени';
                                        break;
                                    case 'daily':
                                        echo 'Посуточная';
                                        break;
                                    default:
                                        echo 'Неизвестно';
                                }
                                ?>
                            </p>
                            <?php if ($order['start_time']): ?>
                                <p><strong>С:</strong> <?= $order['start_time'] ?></p>
                            <?php endif; ?>
                            <?php if ($order['end_time']): ?>
                                <p><strong>До:</strong> <?= $order['end_time'] ?></p>
                            <?php endif; ?>
                            <?php if ($order['total_price']): ?>
                                <p><strong>Стоимость:</strong> <?= $order['total_price'] ?> BYN</p>
                            <?php else: ?>
                                <p><em>Активный заказ (стоимость будет рассчитана позже)</em></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($order['order_type'] === 'open' && !$order['end_time']): ?>
                        <form id="custom-complete-order-form" method="POST" action="finish_order.php">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <button type="button" class="custom-btn-complete" onclick="openCustomConfirmModal()">Завершить заказ</button>
                        </form>

                        <div id="custom-confirm-modal" class="custom-modal-overlay" style="display: none;">
                            <div class="custom-modal-box">
                                <h3>Подтверждение</h3>
                                <p>Вы уверены, что хотите завершить заказ?</p>
                                <div class="custom-modal-buttons">
                                    <button onclick="submitCustomOrderForm()" class="custom-btn-confirm">Да, завершить</button>
                                    <button onclick="closeCustomConfirmModal()" class="custom-btn-cancel">Отмена</button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
    <?php endif; ?>


</main>
    <script>
        function openCustomConfirmModal() {
            document.getElementById('custom-confirm-modal').style.display = 'flex';
        }

        function closeCustomConfirmModal() {
            document.getElementById('custom-confirm-modal').style.display = 'none';
        }

        function submitCustomOrderForm() {
            document.getElementById('custom-complete-order-form').submit();
        }
    </script>


</body>
</html>
