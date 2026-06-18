<?php
session_start();

$pdo = new PDO(
  'mysql:host=localhost;dbname=banket;charset=utf8mb4',
  'root',
  '',
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
  ]
);

// Создаём или обновляем администратора при каждом запуске
try {
  $hash = password_hash('Demo20', PASSWORD_DEFAULT);
  $pdo->prepare("
    INSERT INTO users (login, password_hash, name, phone, email, role)
    VALUES (?, ?, ?, ?, ?, 'admin')
    ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), role = 'admin'
  ")->execute(['Admin26', $hash, 'Администратор', '8(000)000-00-00', 'admin@banketam.net']);
} catch (Exception $e) { /* таблица ещё не создана — ок */ }

function user() {
  return $_SESSION['user'] ?? null;
}

function admin() {
  return user() && user()['role'] === 'admin';
}

function formatDate($date) {
  if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
    return "$m[3].$m[2].$m[1]";
  }
  return $date;
}
