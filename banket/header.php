<header class="nav">
  <a href="index.php" class="logo">Банкетам.Нет</a>

  <button class="burger" onclick="document.querySelector('.nav nav').classList.toggle('open')">☰</button>

  <nav>
    <a href="index.php">Главная</a>
    <a href="catalog.php">Залы</a>
    <?php if (admin()): ?>
      <a href="admin.php">Админ-панель</a>
      <a href="logout.php">Выход (<?= htmlspecialchars(user()['login']) ?>)</a>
    <?php elseif (user()): ?>
      <a href="apply.php">Забронировать</a>
      <a href="orders.php">Мои заявки</a>
      <a href="logout.php">Выход (<?= htmlspecialchars(user()['login']) ?>)</a>
    <?php else: ?>
      <a href="login.php">Вход</a>
      <a href="register.php">Регистрация</a>
    <?php endif; ?>
  </nav>
</header>
