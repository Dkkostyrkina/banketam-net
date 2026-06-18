<?php
require 'db.php';
$halls = $pdo->query('SELECT * FROM halls ORDER BY id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Залы для банкетов</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <h1>Залы для банкетов</h1>
  <div class="cards">
    <?php foreach ($halls as $hall): ?>
      <div class="card">
        <img src="img/<?= htmlspecialchars($hall['image']) ?>" alt="">
        <div class="card-body">
          <h3><?= htmlspecialchars($hall['title']) ?></h3>
          <p><?= htmlspecialchars($hall['description']) ?></p>
          <p class="meta">до <?= (int)$hall['capacity'] ?> чел. &middot; от <?= number_format((int)$hall['price'], 0, '.', ' ') ?> ₽/день</p>
          <?php if (user() && !admin()): ?>
            <a href="apply.php?hall=<?= (int)$hall['id'] ?>" class="btn">Забронировать</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>
