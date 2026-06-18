<?php
require 'db.php';

$login = $name = $phone = $email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $login    = trim($_POST['login'] ?? '');
  $password = $_POST['password'] ?? '';
  $name     = trim($_POST['name'] ?? '');
  $phone    = trim($_POST['phone'] ?? '');
  $email    = trim($_POST['email'] ?? '');

  if (!preg_match('/^[a-zA-Z0-9]{6,}$/', $login))
    $errors['login'] = 'Логин: латиница и цифры, минимум 6 символов';

  if (mb_strlen($password) < 8)
    $errors['password'] = 'Пароль: минимум 8 символов';

  if (!preg_match('/^[А-Яа-яЁё \-]+$/u', $name))
    $errors['name'] = 'ФИО: кириллица, пробелы и дефисы';

  if (!preg_match('/^8\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone))
    $errors['phone'] = 'Телефон в формате 8(XXX)XXX-XX-XX';

  if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors['email'] = 'Некорректный email';

  if (!$errors) {
    $check = $pdo->prepare('SELECT id FROM users WHERE login = ?');
    $check->execute([$login]);
    if ($check->fetch()) $errors['login'] = 'Логин уже занят';
  }

  if (!$errors) {
    $pdo->prepare('INSERT INTO users (login, password_hash, name, phone, email) VALUES (?, ?, ?, ?, ?)')
        ->execute([$login, password_hash($password, PASSWORD_DEFAULT), $name, $phone, $email]);
    header('Location: login.php?registered=1'); exit;
  }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Регистрация &mdash; Банкетам.Нет</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <div class="form-box">
    <h1>Регистрация</h1>
    <form method="post" class="form">
      <label>Логин
        <input name="login" value="<?= htmlspecialchars($login) ?>" autocomplete="username">
      </label>
      <span class="err"><?= $errors['login'] ?? '' ?></span>

      <label>Пароль
        <input type="password" name="password" autocomplete="new-password">
      </label>
      <span class="err"><?= $errors['password'] ?? '' ?></span>

      <label>ФИО
        <input name="name" value="<?= htmlspecialchars($name) ?>">
      </label>
      <span class="err"><?= $errors['name'] ?? '' ?></span>

      <label>Телефон
        <input type="tel" name="phone" class="phone-mask"
               placeholder="8(999)123-45-67" maxlength="15"
               value="<?= htmlspecialchars($phone) ?>">
      </label>
      <span class="err"><?= $errors['phone'] ?? '' ?></span>

      <label>Email
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">
      </label>
      <span class="err"><?= $errors['email'] ?? '' ?></span>

      <button>Зарегистрироваться</button>
      <a href="login.php">Уже зарегистрированы? Вход</a>
    </form>
  </div>
</main>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>
