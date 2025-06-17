<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$user_id = $_SESSION['user_id'];
$car_id = (int) $_POST['car_id'];
$order_type = $_POST['order_type'];
$total_price = null;
$start_time = null;
$end_time = null;

// Получаем информацию о машине
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if (!$car) {
    die("Автомобиль не найден");
}

// Получаем email пользователя
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$email = $user ? $user['email'] : null;

if (!$email) {
    die("Не удалось получить email пользователя");
}

// Обработка типа аренды
switch ($order_type) {
    case 'open':
        $start_time = date('Y-m-d H:i:s');
        break;

    case 'scheduled':
        $start_time = $_POST['start_time_sch'];
        $end_time = $_POST['end_time_sch'];
        $minutes = (strtotime($end_time) - strtotime($start_time)) / 60;
        $total_price = round($minutes * $car['price'], 2);
        break;

    case 'daily':
        $days = max((int)$_POST['days'], 1);
        $start_time = date('Y-m-d H:i:s');
        $end_time = date('Y-m-d H:i:s', strtotime("+$days days"));
        $total_price = round($days * $car['daily_price'], 2);
        break;

    default:
        die("Неверный тип аренды");
}

// Сохраняем заказ
$stmt = $pdo->prepare("INSERT INTO orders (user_id, car_id, start_time, end_time, order_type, total_price) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$user_id, $car_id, $start_time, $end_time, $order_type, $total_price]);

$order_id = $pdo->lastInsertId();

// Отправка письма через PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'godrive.site@gmail.com';           
    $mail->Password = 'eezg cfzm qywq srkd';             
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('godrive.site@gmail.com', 'godrive');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = '=?UTF-8?B?' . base64_encode("Подтверждение заказа №{$order_id} от " . date('d.m.Y H:i')) . '?=';
    /*$mail->MessageID = "<order-{$order_id}-" . time() . "@yourdomain.com>";*/

    $mail->Body = "
        <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
            <h2 style='color: #ff6600;'>Здравствуйте!</h2>
            <p>Вы успешно оформили заказ №<strong>{$order_id}</strong> на автомобиль <strong>{$car['title']}</strong>.</p>
            
            <table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'>Тип аренды:</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>" . strtoupper($order_type) . "</strong></td>
                </tr>
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'>Начало аренды:</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'>{$start_time}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'>Окончание аренды:</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'>" . ($end_time ?: 'По завершению') . "</td>
                </tr>" .
                ($total_price ? "
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'>Итоговая стоимость:</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>{$total_price} BYN</strong></td>
                </tr>" : "") .
            "</table>

            <p style='margin-top: 30px;'>Спасибо, что выбрали наш сервис! 🚗</p>
            <p style='font-size: 12px; color: #999;'>Это письмо отправлено автоматически. Пожалуйста, не отвечайте на него.</p>
        </div>
    ";

    $mail->send();
} catch (Exception $e) {
    error_log("Ошибка при отправке письма: {$mail->ErrorInfo}");
}

// Перенаправление
header("Location: payment.php?order_id=$order_id");
exit();
