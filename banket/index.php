<?php
require 'db.php';
$halls = $pdo->query('SELECT * FROM halls ORDER BY id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Банкетам.Нет — бронирование залов для банкетов</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<section class="hero">
  <div class="hero-text">
    <h1>Банкетам.Нет</h1>
    <p>Информационная система для бронирования помещений для проведения банкетов. Выберите идеальный зал и отпразднуйте незабываемое торжество.</p>
    <a href="catalog.php" class="btn">Смотреть залы</a>
  </div>
</section>

<main class="container">
  <h2>Наши площадки</h2>
  <div class="slider" id="slider">
    <img src="img/1686219637_en-idei-club-p-t.jpg" class="slide active" alt="">
    <img src="img/1686676944_elles-top-p-letnyaya-ploshcha.jpg" class="slide" alt="">
    <img src="img/1671649122_idei-club-p-veranda-.jpg" class="slide" alt="">
    <img src="img/unnamed.jpg" class="slide" alt="">
    <button class="prev" onclick="move(-1)">&#8249;</button>
    <button class="next" onclick="move(1)">&#8250;</button>
  </div>

  <h2>Доступные помещения</h2>
  <div class="cards">
    <?php foreach ($halls as $hall): ?>
      <div class="card">
        <img src="img/<?= htmlspecialchars($hall['image']) ?>" alt="">
        <div class="card-body">
          <h3><?= htmlspecialchars($hall['title']) ?></h3>
          <p><?= htmlspecialchars($hall['description']) ?></p>
          <p class="meta">до <?= (int)$hall['capacity'] ?> чел. &middot; от <?= number_format((int)$hall['price'], 0, '.', ' ') ?> ₽</p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <h2>Почему мы</h2>
  <div class="features">
    <div class="feature"><h3>Удобство</h3><p>Бронирование онлайн в несколько кликов. Без очередей и лишних звонков.</p></div>
    <div class="feature"><h3>Выбор</h3><p>Зал, ресторан, летняя и закрытая веранды. Найдём подходящее под ваше мероприятие.</p></div>
    <div class="feature"><h3>Надёжность</h3><p>Менеджер свяжется с вами в течение дня. Гарантируем высокое качество обслуживания.</p></div>
  </div>
</main>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>
