<header class="page-header">
  <a class="page-header__logo" href="/">
    <img src="img/logo.svg" alt="Fashion">
  </a>
  <nav class="page-header__menu">
    <ul class="main-menu main-menu--header">
      <li>
        <a class="main-menu__item" href="/">Главная</a>
      </li>
      <li>
        <a class="main-menu__item" href="/products.php">Товары</a>
      </li>
      <li>
        <a class="main-menu__item" href="/orders.php">Заказы</a>
      </li>
      <li>
        <a class="main-menu__item" href="
<?php
if (isset($_SESSION['is_authorized'])) { 
  echo '/src/off_authorization.php';
} else {
  echo '';
}
?>
">Выйти</a>
      </li>
    </ul>
  </nav>
</header>
