<?php
require 'db.php';
if (!admin()) { header('Location: login.php'); exit; }

$statuses = ['Новая', 'Банкет назначен', 'Банкет завершен'];

// Смена статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'status') {
  $newStatus = $_POST['status'] ?? '';
  $orderId   = (int)($_POST['id'] ?? 0);
  if (in_array($newStatus, $statuses) && $orderId > 0) {
    $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$newStatus, $orderId]);
    header('Location: admin_orders.php?success=1'); exit;
  }
}

// Удаление
if (($_GET['delete'] ?? 0) > 0) {
  $pdo->prepare('DELETE FROM orders WHERE id = ?')->execute([(int)$_GET['delete']]);
  header('Location: admin_orders.php'); exit;
}

$message = isset($_GET['success']) ? 'Статус заявки изменён' : '';

$filter = $_GET['status'] ?? '';
if ($filter !== '' && !in_array($filter, $statuses)) $filter = '';

$sortField  = in_array($_GET['sort'] ?? '', ['id', 'start_date', 'status']) ? $_GET['sort'] : 'id';
$sortDir    = ($_GET['dir'] ?? '') === 'asc' ? 'ASC' : 'DESC';
$nextDir    = $sortDir === 'DESC' ? 'asc' : 'desc';

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 5;
$offset  = ($page - 1) * $perPage;

$where  = $filter !== '' ? 'WHERE orders.status = ?' : '';
$params = $filter !== '' ? [$filter] : [];

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM orders $where");
$totalStmt->execute($params);
$total = $totalStmt->fetchColumn();
$pages = max(1, ceil($total / $perPage));

$listStmt = $pdo->prepare("
  SELECT orders.*, users.name AS user_name, users.login, halls.title AS hall_title
  FROM orders
  JOIN users ON users.id = orders.user_id
  LEFT JOIN halls ON halls.id = orders.hall_id
  $where
  ORDER BY $sortField $sortDir
  LIMIT $perPage OFFSET $offset");
$listStmt->execute($params);
$orders = $listStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Заявки &mdash; Банкетам.Нет</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
  <?php include 'admin_tabs.php'; ?>
  <h1>Заявки пользователей</h1>

  <?php if ($message): ?>
    <div class="toast" id="toast"><?= $message ?></div>
  <?php endif; ?>

  <form method="get" class="filter">
    <label>Фильтр по статусу:
      <select name="status" onchange="this.form.submit()">
        <option value="">Все</option>
        <?php foreach ($statuses as $s): ?>
          <option <?= $filter === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <input type="hidden" name="sort" value="<?= htmlspecialchars($sortField) ?>">
    <input type="hidden" name="dir"  value="<?= $sortDir === 'DESC' ? 'desc' : 'asc' ?>">
  </form>

  <table class="tbl">
    <tr>
      <th><a href="?status=<?= urlencode($filter) ?>&sort=id&dir=<?= $sortField==='id'?$nextDir:'desc' ?>">#</a></th>
      <th>Пользователь</th>
      <th>Зал</th>
      <th><a href="?status=<?= urlencode($filter) ?>&sort=start_date&dir=<?= $sortField==='start_date'?$nextDir:'desc' ?>">Дата</a></th>
      <th>Оплата</th>
      <th><a href="?status=<?= urlencode($filter) ?>&sort=status&dir=<?= $sortField==='status'?$nextDir:'desc' ?>">Статус</a></th>
      <th></th>
    </tr>
    <?php foreach ($orders as $order): ?>
      <tr>
        <td><?= (int)$order['id'] ?></td>
        <td><?= htmlspecialchars($order['user_name']) ?> (<?= htmlspecialchars($order['login']) ?>)</td>
        <td><?= htmlspecialchars($order['hall_title'] ?? '(удалён)') ?></td>
        <td><?= formatDate($order['start_date']) ?></td>
        <td><?= htmlspecialchars($order['payment']) ?></td>
        <td>
          <form method="post">
            <input type="hidden" name="action" value="status">
            <input type="hidden" name="id"     value="<?= (int)$order['id'] ?>">
            <select name="status" onchange="confirmStatus(this)">
              <?php foreach ($statuses as $s): ?>
                <option <?= $order['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
          </form>
        </td>
        <td>
          <a href="?delete=<?= (int)$order['id'] ?>&status=<?= urlencode($filter) ?>"
             class="del" onclick="return confirm('Удалить заявку?')">&times;</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <div class="pages">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <a href="?status=<?= urlencode($filter) ?>&sort=<?= $sortField ?>&dir=<?= strtolower($sortDir) ?>&page=<?= $i ?>"
         class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
</main>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
<script>
function confirmStatus(sel) {
  if (confirm('Изменить статус на «' + sel.value + '»?')) {
    sel.form.submit();
  } else {
    // вернуть предыдущее значение
    sel.value = sel.querySelector('[selected]')?.value || sel.options[0].value;
  }
}
// Авто-скрытие toast через 3 секунды
const toast = document.getElementById('toast');
if (toast) setTimeout(() => toast.style.opacity = '0', 3000);
</script>
</body>
</html>
