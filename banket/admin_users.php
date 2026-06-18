<?php
require 'db.php';
if (!admin()) { header('Location: login.php'); exit; }

// Удаление (нельзя себя)
if (($_GET['delete'] ?? 0) > 0 && (int)$_GET['delete'] !== user()['id']) {
  $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([(int)$_GET['delete']]);
  header('Location: admin_users.php'); exit;
}

// Смена роли
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'role') {
  $newRole = in_array($_POST['role'] ?? '', ['user', 'admin']) ? $_POST['role'] : 'user';
  $pdo->prepare('UPDATE users SET role = ? WHERE id = ?')->execute([$newRole, (int)$_POST['id']]);
  header('Location: admin_users.php'); exit;
}

$users = $pdo->query('SELECT id, login, name, phone, email, role FROM users ORDER BY id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Пользователи &mdash; Банкетам.Нет</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <?php include 'admin_tabs.php'; ?>
  <h1>Пользователи</h1>

  <table class="tbl">
    <tr><th>Логин</th><th>ФИО</th><th>Телефон</th><th>Email</th><th>Роль</th><th></th></tr>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= htmlspecialchars($u['login']) ?></td>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= htmlspecialchars($u['phone']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td>
          <form method="post">
            <input type="hidden" name="action" value="role">
            <input type="hidden" name="id"     value="<?= (int)$u['id'] ?>">
            <select name="role" onchange="this.form.submit()">
              <option value="user"  <?= $u['role'] === 'user'  ? 'selected' : '' ?>>user</option>
              <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
            </select>
          </form>
        </td>
        <td>
          <?php if ($u['id'] !== user()['id']): ?>
            <a href="?delete=<?= (int)$u['id'] ?>" class="del"
               onclick="return confirm('Удалить пользователя?')">&times;</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
