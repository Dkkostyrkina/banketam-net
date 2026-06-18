<?php
require 'db.php';
if (!user()) { header('Location: login.php'); exit; }
if (admin()) { header('Location: admin.php'); exit; }

$halls = $pdo->query('SELECT id, title FROM halls ORDER BY title')->fetchAll();
$hallIds = array_column($halls, 'id');

$minDate = date('Y-m-d');
$maxDate = date('Y-m-d', strtotime('+2 years'));

$hallId   = (int)($_GET['hall'] ?? 0);
$startDate = '';
$payment   = '';
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $hallId    = (int)($_POST['hall_id'] ?? 0);
  $startDate = trim($_POST['start_date'] ?? '');
  $payment   = $_POST['payment'] ?? '';

  if (!in_array($hallId, $hallIds))
    $errors['hall_id'] = 'Выберите помещение из списка';

  // Дата может приходить как ISO (input type=date) или как ДД.ММ.ГГГГ
  if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $startDate, $m)) {
    $startDate = "$m[3]-$m[2]-$m[1]";
  }
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
    $errors['start_date'] = 'Укажите дату в формате ДД.ММ.ГГГГ';
  } elseif ($startDate < $minDate || $startDate > $maxDate) {
    $errors['start_date'] = 'Дата должна быть от сегодня до 2 лет вперёд';
  }

  if (!in_array($payment, ['Наличные', 'Банковская карта', 'Перевод']))
    $errors['payment'] = 'Выберите способ оплаты';

  if (!$errors) {
    $pdo->prepare('INSERT INTO orders (user_id, hall_id, start_date, payment) VALUES (?, ?, ?, ?)')
        ->execute([user()['id'], $hallId, $startDate, $payment]);
    header('Location: orders.php'); exit;
  }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Заявка на бронирование &mdash; Банкетам.Нет</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <h1>Заявка на бронирование</h1>
  <form method="post" class="form">

    <label>Помещение
      <select name="hall_id">
        <option value="" disabled <?= $hallId === 0 ? 'selected' : '' ?>>&mdash; выберите зал &mdash;</option>
        <?php foreach ($halls as $h): ?>
          <option value="<?= (int)$h['id'] ?>" <?= $hallId === (int)$h['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($h['title']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <span class="err"><?= $errors['hall_id'] ?? '' ?></span>

    <label>Дата начала банкета
      <input type="text" name="start_date" class="date-mask"
             placeholder="ДД.ММ.ГГГГ" maxlength="10"
             value="<?= $startDate ? formatDate($startDate) : '' ?>">
    </label>
    <span class="err"><?= $errors['start_date'] ?? '' ?></span>

    <label>Способ оплаты
      <select name="payment">
        <option value="" disabled <?= $payment === '' ? 'selected' : '' ?>>&mdash; выберите способ &mdash;</option>
        <option <?= $payment === 'Наличные' ? 'selected' : '' ?>>Наличные</option>
        <option <?= $payment === 'Банковская карта' ? 'selected' : '' ?>>Банковская карта</option>
        <option <?= $payment === 'Перевод' ? 'selected' : '' ?>>Перевод</option>
      </select>
    </label>
    <span class="err"><?= $errors['payment'] ?? '' ?></span>

    <button>Отправить заявку</button>
  </form>
</main>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>
