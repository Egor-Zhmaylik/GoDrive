<?php
session_start();
require 'config.php';

$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id'] ?? 0]);
$user = $stmt->fetch();

if (!$user || !$user['is_admin']) {
    die("Доступ запрещен");
}

$cars = $pdo->query("SELECT * FROM cars")->fetchAll();
$users = $pdo->query("SELECT id, CONCAT(lastname, ' ', firstname, ' ', middlename) AS full_name, email FROM users")->fetchAll();
$stmt = $pdo->query("
    SELECT o.id, CONCAT(u.lastname, ' ', u.firstname) AS user_name, c.title AS car_title,
           o.start_time, o.end_time, o.order_type, o.total_price, o.created_at, o.is_paid
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN cars c ON o.car_id = c.id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель | GoDrive</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <style>
        body {
            font-family: Arial;
            padding: 20px;
            background: #f2f2f2;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .tabs button {
            padding: 10px 20px;
            border: none;
            background: #ccc;
            cursor: pointer;
            border-radius: 5px;
        }
        .tabs button.active {
            background: #ED872D;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .admin-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-delete {
            background-color: #ff4d4d;
            color: white;
        }
        .btn-add {
            background-color: orange;
            color: white;
        }
        .add-form input {
            margin-bottom: 5px;
            padding: 6px;
            width: 100%;
        }
        .add-form {
            margin-bottom: 20px;
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            max-width: 500px;
        }
        table img {
            max-width: 100px;
        }
    </style>
</head>
<body>

<h1>Админ-панель GoDrive
    <a href="index.php" style="margin-left: 20px; padding: 7px 10px; background:#ED872D; color:white; text-decoration:none; border-radius:5px;">← Назад</a>
</h1>

<div class="tabs">
    <button class="tab-btn active" data-tab="cars">🚗 Автомобили</button>
    <button class="tab-btn" data-tab="users">👥 Пользователи</button>
    <button class="tab-btn" data-tab="orders">📦 Заказы</button>
</div>

<div id="cars" class="tab-content active">
    <h2>➕ Добавить автомобиль</h2>
    <form class="add-form" action="admin_actions/add_car.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Название" required>
        <input type="number" step="0.01" name="price" placeholder="Цена за минуту" required>
        <input type="number" step="0.01" name="daily_price" placeholder="Цена за день" required>
        <input type="file" name="photo" accept="image/*" required>
        <button type="submit" class="admin-btn btn-add">Добавить</button>
    </form>

    <h2>🚘 Список автомобилей</h2>
    <table id="carsTable" class="display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Дневная цена</th>
                <th>Фото</th>
                <th>Действия</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($cars as $car): ?>
            <tr>
                <td><?= $car['id'] ?></td>
                <td><?= htmlspecialchars($car['title']) ?></td>
                <td><?= $car['price'] ?> BYN/мин</td>
                <td><?= $car['daily_price'] ?> BYN/день</td>
                <td><img src="carphotoes/<?= $car['image'] ?>" alt=""></td>
                <td>
                    <form action="admin_actions/delete_car.php" method="POST" style="display:inline;">
                        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                        <button class="admin-btn btn-delete">Удалить</button>
                    </form>
                    <a href="admin_actions/edit_car.php?id=<?= $car['id'] ?>" class="admin-btn" style="background:#4CAF50;color:white;margin-left:5px;">✏️ Редактировать</a>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="users" class="tab-content">
    <h2>👥 Пользователи</h2>
    <table id="usersTable" class="display">
        <thead>
            <tr><th>ID</th><th>Имя</th><th>Email</th><th>Удалить</th></tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['full_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td>
                    <form action="admin_actions/delete_user.php" method="POST">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button class="admin-btn btn-delete">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="orders" class="tab-content">
    <h2>📦 Заказы</h2>
    <table id="ordersTable" class="display">
        <thead>
            <tr><th>ID</th><th>Пользователь</th><th>Авто</th><th>Тип</th><th>Начало</th><th>Конец</th><th>Сумма</th><th>Удалить</th></tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['id'] ?></td>
                <td><?= $order['user_name'] ? htmlspecialchars($order['user_name']) : '<em>Удален</em>' ?></td>
                <td><?= $order['car_title'] ? htmlspecialchars($order['car_title']) : '<em>Удалено</em>' ?></td>
                <td><?= $order['order_type'] ?></td>
                <td><?= $order['start_time'] ?></td>
                <td><?= $order['end_time'] ?: '—' ?></td>
                <td><?= $order['total_price'] ?: '—' ?> BYN</td>
                <td>
                    <form action="admin_actions/delete_order.php" method="POST">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button class="admin-btn btn-delete">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $('#carsTable').DataTable({
            columnDefs: [
                { orderable: false, targets: [4, 5] } // Фото и Удалить
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/ru.json'
            }

        });

        $('#usersTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/ru.json'
            }

        });

        $('#ordersTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/ru.json'
            }

        });

        $('.tab-btn').on('click', function () {
            $('.tab-btn').removeClass('active');
            $(this).addClass('active');

            $('.tab-content').removeClass('active');
            $('#' + $(this).data('tab')).addClass('active');
        });
    });
</script>


</body>
</html>
