<?php
$host = 'localhost'; // Или IP-адрес сервера
$dbname = 'godrive_db'; // Имя базы данных
$username = 'root'; // Имя пользователя MySQL
$password = ''; // Пароль MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>
