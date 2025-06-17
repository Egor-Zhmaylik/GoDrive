<?php
session_start();
require 'config.php';

$errors = [];

function isRussian($value) {
    return preg_match("/^[А-Яа-яЁё\s\-]+$/u", $value);
}

if (!isRussian($_POST['lastname'])) {
    $errors[] = "Фамилия должна содержать только русские буквы.";
}
if (!isRussian($_POST['firstname'])) {
    $errors[] = "Имя должна содержать только русские буквы.";
}
if (!isRussian($_POST['middlename'])) {
    $errors[] = "Отчество должно содержать только русские буквы.";
}
if (!preg_match("/^\+375\(\d{2}\)\d{7}$/", $_POST['phone'])) {
    $errors[] = "Телефон должен быть в формате +375(XX)XXXXXXX.";
}


if (!preg_match("/^[0-9]{7}[A-Z]{2}[0-9]{7}$/", $_POST['passport_number'])) {
    $errors[] = "Номер паспорта должен быть в формате: 1234567AB1234567";
}


if (!preg_match("/^[0-9]{1}[A-Z]{2} [0-9]{7}$/", $_POST['driver_license'])) {
    $errors[] = "Номер ВУ должен быть в формате: xYY xxxxxxx";
}
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Некорректный email.";
}
if (strlen($_POST['password']) < 8) {
    $errors[] = "Пароль должен быть не менее 8 символов.";
}

if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    header("Location: index.php#register");
    exit();
}

$lastname = trim($_POST["lastname"]);
$firstname = trim($_POST["firstname"]);
$middlename = trim($_POST["middlename"]);
$phone = trim($_POST["phone"]);
$passport_number = trim($_POST["passport_number"]);
$driver_license = trim($_POST["driver_license"]);
$email = trim($_POST["email"]);
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);

$checkStmt = $pdo->prepare("SELECT id FROM users WHERE phone = ? OR email = ?");
$checkStmt->execute([$phone, $email]);

if ($checkStmt->rowCount() > 0) {
    $_SESSION['register_errors'] = ["Пользователь с таким email или телефоном уже существует!"];
    header("Location: index.php#register");
    exit();
}

$stmt = $pdo->prepare("INSERT INTO users (lastname, firstname, middlename, phone, passport_number, driver_license, email, password) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt->execute([$lastname, $firstname, $middlename, $phone, $passport_number, $driver_license, $email, $password])) {
    $_SESSION['register_success'] = "Регистрация прошла успешно! Теперь вы можете войти.";
    header("Location: index.php#login");
    exit();
} else {
    $_SESSION['register_errors'] = ["Ошибка регистрации: попробуйте позже."];
    header("Location: index.php#register");
    exit();
}
?>
