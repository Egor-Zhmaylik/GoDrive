<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$order_id = (int) ($_GET['order_id'] ?? 0);
$stmt = $pdo->prepare("SELECT o.*, c.title FROM orders o JOIN cars c ON o.car_id = c.id WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    die("Заказ не найден");
}

$errors = $_SESSION['payment_errors'] ?? [];
$success = $_SESSION['payment_success'] ?? false;

unset($_SESSION['payment_errors'], $_SESSION['payment_success']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оплата заказа #<?= $order_id ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .payment-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 400px;
        }
        input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input[type="submit"] {
            background-color: orange;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: darkorange;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .success {
            color: green;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h2>Оплата заказа #<?= $order_id ?></h2>
        <p><strong>Автомобиль:</strong> <?= htmlspecialchars($order['title']) ?></p>
        <p><strong>Сумма к оплате:</strong> <?= $order['total_price'] ?: 'по завершению' ?> BYN</p>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <div><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success">✅ Оплата прошла успешно!</div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="profile.php" style="
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: orange;
                        color: white;
                        text-decoration: none;
                        border-radius: 6px;
                        font-weight: bold;
                        transition: background-color 0.3s ease;
                    ">Вернуться в личный кабинет</a>
                </div> 
        <?php else: ?>
            <form action="process_payment.php" method="post">
                <input type="hidden" name="order_id" value="<?= $order_id ?>">
                
                <label>Номер карты</label>
                <input type="text" name="card_number" required placeholder="0000 0000 0000 0000">

                <label>Срок действия</label>
                <input type="text" name="expiry" required placeholder="MM/YY">

                <label>CVV</label>
                <input type="text" name="cvv" required placeholder="123">

                <input type="submit" value="Оплатить">
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
