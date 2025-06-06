<?php
require 'config.php';

$stmt = $pdo->query("SELECT id FROM cars ORDER BY RAND() LIMIT 1");
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if ($car) {
    echo json_encode(['success' => true, 'id' => $car['id']]);
} else {
    echo json_encode(['success' => false]);
}
