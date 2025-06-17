<?php
session_start();
require 'config.php';

if ($_GET['car'] === 'random') {
    $stmt = $pdo->query("SELECT * FROM cars ORDER BY RAND() LIMIT 1");
    $car = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($car) {
        $carId = $car['id'];
        header("Location: order.php?id=$carId");
        exit();
    } else {
        echo "Нет доступных автомобилей.";
        exit();
    }
}


if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['id'])) {
    echo "Автомобиль не найден.";
    exit();
}

$carId = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$carId]);
$car = $stmt->fetch();

if (!$car) {
    echo "Автомобиль не найден.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оформление заказа</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .order-container {
            max-width: 800px;
            margin: 40px auto;
            background: rgba(255,255,255,0.95);
            padding: 30px;
            border-radius: 10px;
        }

        .order-container h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .car-preview {
            text-align: center;
            margin-bottom: 30px;
        }

        .car-preview img {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        form label {
            display: block;
            margin: 15px 0 5px;
        }

        form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
        }

        .hidden {
            display: none;
        }

        .car-btn {
            width: 100%;
        }

        .tariff-buttons {
        display: flex;
        gap: 20px;
        margin: 15px 0;
        flex-wrap: wrap;
        justify-content: space-between;
        }

        .tariff-btn {
            flex: 1;
            min-width: 30%;
            padding: 20px;
            font-size: 16px;
            text-align: center;
            border: 2px solid #444;
            border-radius: 10px;
            background-color: #f0f0f0;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #222;
        }

        .tariff-btn span {
            font-size: 14px;
            color: #555;
        }

        .tariff-btn:hover {
            background-color: #ddd;
        }

        .tariff-btn.selected {
            background-color: #bbbbbb;
            color: white;
            border-color: #222;
        }
        .text {
            color: #ff6600; /* ярко-оранжевый, можешь подстроить под стиль кнопок */
        }

    </style>
</head>
<body>

<div class="order-container">
    <a href="javascript:history.back()" class="car-btn" style="margin-bottom: 20px; display: inline-block;">← Назад</a>

    <h2 class="text">Оформление заказа</h2>

    <div class="car-preview">
        <img src="carphotoes/<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['title']) ?>">
        <h3 class="text"><?= htmlspecialchars($car['title']) ?></h3>
    </div>

    <form action="process_order.php" method="post" id="order-form">
        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">

        <label class="text" style="margin-top: 30px;">Выберите тип аренды:</label>
        <div class="tariff-buttons">
            <input type="hidden" name="order_type" id="order_type" required>

            <button type="button" class="tariff-btn" data-type="open">
                Сейчас<br><span>До завершения • <?= number_format($car['price'], 2) ?> BYN/мин</span>
            </button>
            <button type="button" class="tariff-btn" data-type="scheduled">
                По времени<br><span>Поминутно • <?= number_format($car['price'], 2) ?> BYN/мин</span>
            </button>
            <button type="button" class="tariff-btn" data-type="daily">
                Посуточно<br><span><?= number_format($car['daily_price'], 2) ?> BYN/сутки</span>
            </button>
        </div>


        <div id="scheduled-fields" class="hidden">
            <label class="text">Начало аренды:</label>
            <input type="datetime-local" name="start_time_sch">

            <label class="text">Окончание аренды:</label>
            <input type="datetime-local" name="end_time_sch">
        </div>

        <div id="daily-fields" class="hidden">
            <label class="text">Количество суток:</label>
            <input type="number" name="days" min="1">
        </div>

        <button type="submit" class="car-btn">Подтвердить заказ</button>
        <div id="error-message" style="color: red; text-align: center; margin-top: 10px;"></div>

    </form>
</div>

<script>
    const orderTypeInput = document.getElementById('order_type');
    const tariffButtons = document.querySelectorAll('.tariff-btn');
    const scheduledFields = document.getElementById('scheduled-fields');
    const dailyFields = document.getElementById('daily-fields');

    tariffButtons.forEach(button => {
        button.addEventListener('click', () => {
            tariffButtons.forEach(btn => btn.classList.remove('selected'));
            button.classList.add('selected');

            const selectedType = button.getAttribute('data-type');
            orderTypeInput.value = selectedType;

            scheduledFields.classList.add('hidden');
            dailyFields.classList.add('hidden');

            if (selectedType === 'scheduled') {
                scheduledFields.classList.remove('hidden');
            } else if (selectedType === 'daily') {
                dailyFields.classList.remove('hidden');
            }
        });
    });
    document.getElementById('order-form').addEventListener('submit', function (e) {
        const selectedType = orderTypeInput.value;
        const errorDiv = document.getElementById('error-message');
        errorDiv.textContent = ''; 

        if (selectedType === 'scheduled') {
            const start = document.querySelector('input[name="start_time_sch"]').value;
            const end = document.querySelector('input[name="end_time_sch"]').value;

            if (!start || !end) {
                e.preventDefault();
                errorDiv.textContent = 'Пожалуйста, укажите время начала и окончания аренды.';
                return;
            }

            if (new Date(start) >= new Date(end)) {
                e.preventDefault();
                errorDiv.textContent = 'Дата окончания должна быть позже даты начала.';
                return;
            }
        }

        if (selectedType === 'daily') {
            const days = document.querySelector('input[name="days"]').value;
            if (!days || parseInt(days) <= 0) {
                e.preventDefault();
                errorDiv.textContent = 'Укажите количество суток.';
                return;
            }
        }
    });

</script>


</body>
</html>
