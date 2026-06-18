<?php
require 'db.php';
if (!admin()) { header('Location: login.php'); exit; }

// Удаление
if (($_GET['delete'] ?? 0) > 0) {
  $pdo->prepare('DELETE FROM halls WHERE id = ?')->execute([(int)$_GET['delete']]);
  header('Location: admin_catalog.php'); exit;
}

$edit   = ['id' => 0, 'title' => '', 'description' => '', 'capacity' => 0, 'price' => 0, 'image' => ''];
$errors = [];

// Создание / обновление
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $edit['id']          = (int)($_POST['id'] ?? 0);
  $edit['title']       = trim($_POST['title'] ?? '');
  $edit['description'] = trim($_POST['description'] ?? '');
  $edit['capacity']    = (int)($_POST['capacity'] ?? 0);
  $edit['price']       = (int)($_POST['price'] ?? 0);
  $edit['image']       = trim($_POST['image_current'] ?? 'unnamed.jpg');

  if ($edit['title'] === '')
    $errors['title'] = 'Введите название';
  if ($edit['capacity'] < 0)
    $errors['capacity'] = 'Вместимость не может быть отрицательной';
  if ($edit['price'] < 0)
    $errors['price'] = 'Цена не может быть отрицательной';

  if (!$errors && !empty($_FILES['image_file']['tmp_name'])) {
    $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
      $edit['image'] = 'upload_' . time() . '.' . $ext;
      move_uploaded_file($_FILES['image_file']['tmp_name'], __DIR__ . '/img/' . $edit['image']);
    }
  }

  if (!$errors) {
    if ($edit['id'] > 0) {
      $pdo->prepare('UPDATE halls SET title=?, description=?, capacity=?, price=?, image=? WHERE id=?')
          ->execute([$edit['title'], $edit['description'], $edit['capacity'], $edit['price'], $edit['image'], $edit['id']]);
    } else {
      $pdo->prepare('INSERT INTO halls (title, description, capacity, price, image) VALUES (?, ?, ?, ?, ?)')
          ->execute([$edit['title'], $edit['description'], $edit['capacity'], $edit['price'], $edit['image']]);
    }
    header('Location: admin_catalog.php'); exit;
  }
}

if (!$errors && ($_GET['edit'] ?? 0) > 0) {
  $row = $pdo->prepare('SELECT * FROM halls WHERE id = ?');
  $row->execute([(int)$_GET['edit']]);
  $edit = $row->fetch() ?: $edit;
}

$halls = $pdo->query('SELECT * FROM halls ORDER BY id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Управление залами &mdash; Банкетам.Нет</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <?php include 'admin_tabs.php'; ?>
  <h1>Управление залами</h1>

  <table class="tbl">
    <tr><th>Название</th><th>Вместимость</th><th>Цена/день</th><th>Фото</th><th></th></tr>
    <?php foreach ($halls as $h): ?>
      <tr>
        <td><?= htmlspecialchars($h['title']) ?></td>
        <td>до <?= (int)$h['capacity'] ?> чел.</td>
        <td><?= number_format((int)$h['price'], 0, '.', ' ') ?> ₽</td>
        <td><img src="img/<?= htmlspecialchars($h['image']) ?>" class="thumb" alt=""></td>
        <td>
          <a href="?edit=<?= (int)$h['id'] ?>">&#9998;</a>
          <a href="?delete=<?= (int)$h['id'] ?>" class="del" onclick="return confirm('Удалить зал?')">&times;</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h2><?= $edit['id'] ? 'Редактировать зал' : 'Добавить зал' ?></h2>
  <form method="post" class="form" enctype="multipart/form-data">
    <input type="hidden" name="id"            value="<?= (int)$edit['id'] ?>">
    <input type="hidden" name="image_current" value="<?= htmlspecialchars($edit['image']) ?>">

    <label>Название
      <input name="title" required value="<?= htmlspecialchars($edit['title']) ?>">
    </label>
    <span class="err"><?= $errors['title'] ?? '' ?></span>

    <label>Описание
      <textarea name="description"><?= htmlspecialchars($edit['description']) ?></textarea>
    </label>

    <label>Вместимость (чел.)
      <input type="number" name="capacity" min="0" value="<?= (int)$edit['capacity'] ?>">
    </label>
    <span class="err"><?= $errors['capacity'] ?? '' ?></span>

    <label>Цена в день (₽)
      <input type="number" name="price" min="0" value="<?= (int)$edit['price'] ?>">
    </label>
    <span class="err"><?= $errors['price'] ?? '' ?></span>

    <label>Фото (JPG / PNG / WEBP)
      <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp">
    </label>
    <?php if ($edit['image']): ?>
      <p>Текущая: <img src="img/<?= htmlspecialchars($edit['image']) ?>" class="thumb" alt=""></p>
    <?php endif; ?>

    <button><?= $edit['id'] ? 'Сохранить' : 'Добавить' ?></button>
    <?php if ($edit['id']): ?><a href="admin_catalog.php">Отмена</a><?php endif; ?>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
