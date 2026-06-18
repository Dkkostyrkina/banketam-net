<?php
require 'db.php';
if (!user()) { header('Location: login.php'); exit; }
if (admin()) { header('Location: admin.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $reviewText = trim($_POST['review'] ?? '');
  $orderId    = (int)($_POST['id'] ?? 0);
  if ($reviewText !== '' && $orderId > 0) {
    $pdo->prepare('UPDATE orders SET review = ? WHERE id = ? AND user_id = ? AND status = "Банкет завершен"')
        ->execute([$reviewText, $orderId, user()['id']]);
  }
  header('Location: orders.php'); exit;
}

$stmt = $pdo->prepare('
  SELECT orders.*, halls.title AS hall_title
  FROM orders
  LEFT JOIN halls ON halls.id = orders.hall_id
  WHERE orders.user_id = ?
  ORDER BY orders.id DESC');
$stmt->execute([user()['id']]);
$orders = $stmt->fetchAll();

$badgeClass = [
  'Новая'            => 'badge-new',
  'Банкет назначен'  => 'badge-progress',
  'Банкет завершен'  => 'badge-done',
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Мои заявки &mdash; Банкетам.Нет</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <h1>Мои заявки</h1>

  <!-- Слайдер в личном кабинете -->
  <div class="slider" id="slider" style="height:240px;margin-bottom:30px">
    <img src="img/1686219637_en-idei-club-p-t.jpg" class="slide active" alt="">
    <img src="img/1686676944_elles-top-p-letnyaya-ploshcha.jpg" class="slide" alt="">
    <img src="img/unnamed.jpg" class="slide" alt="">
    <img src="img/1671649122_idei-club-p-veranda-.jpg" class="slide" alt="">
    <button class="prev" onclick="move(-1)">&#8249;</button>
    <button class="next" onclick="move(1)">&#8250;</button>
  </div>

  <?php if (!$orders): ?>
    <div class="empty">
      <p>У вас пока нет заявок.</p>
      <a href="catalog.php" class="btn">Смотреть залы</a>
    </div>
  <?php endif; ?>

  <div class="orders">
    <?php foreach ($orders as $order): ?>
      <?php $bc = $badgeClass[$order['status']] ?? 'badge-new'; ?>
      <article class="order order--<?= $bc ?>">
        <header class="order-head">
          <h3><?= htmlspecialchars($order['hall_title'] ?? '(зал удалён)') ?></h3>
          <span class="badge <?= $bc ?>"><?= htmlspecialchars($order['status']) ?></span>
        </header>

        <div class="order-info">
          <div><span>Дата банкета</span><b><?= formatDate($order['start_date']) ?></b></div>
          <div><span>Способ оплаты</span><b><?= htmlspecialchars($order['payment']) ?></b></div>
        </div>

        <?php if ($order['status'] === 'Банкет завершен'): ?>
          <?php if ($order['review']): ?>
            <div class="review">
              <span>Ваш отзыв:</span>
              <p><?= htmlspecialchars($order['review']) ?></p>
            </div>
          <?php else: ?>
            <form method="post" class="form review-form">
              <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
              <label>Оставьте отзыв о банкете
                <textarea name="review" required placeholder="Расскажите, как прошло торжество..."></textarea>
              </label>
              <button>Отправить отзыв</button>
            </form>
          <?php endif; ?>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  </div>
</main>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>
