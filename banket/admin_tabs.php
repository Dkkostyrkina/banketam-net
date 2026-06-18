<?php $page = basename($_SERVER['PHP_SELF']); ?>
<nav class="tabs">
  <a href="admin.php"          class="<?= $page === 'admin.php'          ? 'active' : '' ?>">Сводка</a>
  <a href="admin_orders.php"   class="<?= $page === 'admin_orders.php'   ? 'active' : '' ?>">Заявки</a>
  <a href="admin_catalog.php"  class="<?= $page === 'admin_catalog.php'  ? 'active' : '' ?>">Залы</a>
  <a href="admin_users.php"    class="<?= $page === 'admin_users.php'    ? 'active' : '' ?>">Пользователи</a>
</nav>
