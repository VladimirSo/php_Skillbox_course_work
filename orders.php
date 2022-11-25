<?php
require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/check_authorized.php');
checkAuth();

require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/db_connect.php');
$pdo = getDbConnect();
//обработка запроса на изменение статуса заказа 
if (isset($_GET['done'])) {
  //получаем текущие сведения
  $stmt = $pdo -> prepare("
    SELECT status
    FROM ordering
    WHERE id = :id");

  $stmt -> execute(['id' => $_GET['id']]);
  $order = $stmt->fetch(PDO::FETCH_LAZY);

  $stmt = $pdo -> prepare("
    UPDATE ordering 
    SET status = :status
    WHERE id = :id");
  //меняем статус на противоположный
  $status = $order['status'] === 0 ? 1 : 0; 
  $stmt -> execute(['id' => $_GET['id'], 'status' => $status]);
  $stmt->fetch(PDO::FETCH_LAZY);

  header( 'Location: /orders.php' );
}
//выборка списка заказов
$stmt = $pdo -> query("SELECT * FROM ordering");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Список заказов</title>

  <meta name="description" content="Fashion - интернет-магазин">
  <meta name="keywords" content="Fashion, интернет-магазин, одежда, аксессуары">

  <meta name="theme-color" content="#393939">

  <link rel="preload" href="fonts/opensans-400-normal.woff2" as="font">
  <link rel="preload" href="fonts/roboto-400-normal.woff2" as="font">
  <link rel="preload" href="fonts/roboto-700-normal.woff2" as="font">

  <link rel="icon" href="img/favicon.png">
  <link rel="stylesheet" href="css/style.min.css">
  <script src="js/scripts.js" defer=""></script>
</head>
<body>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . ('/templates/admin_header.php');
?>
<main class="page-order">
  <h1 class="h h--1">Список заказов</h1>
  <ul class="page-order__list">
    <li class="order-item page-order__item">
      <div class="order-item__wrapper">
        <div class="order-item__group order-item__group--id">
          <span class="order-item__title">Номер заказа</span>
          <span class="order-item__info order-item__info--id">235454345</span>
        </div>
        <div class="order-item__group">
          <span class="order-item__title">Сумма заказа</span>
          10 400 руб.
        </div>
        <button class="order-item__toggle"></button>
      </div>
      <div class="order-item__wrapper">
        <div class="order-item__group order-item__group--margin">
          <span class="order-item__title">Заказчик</span>
          <span class="order-item__info">Смирнов Павел Владимирович</span>
        </div>
        <div class="order-item__group">
          <span class="order-item__title">Номер телефона</span>
          <span class="order-item__info">+7 987 654 32 10</span>
        </div>
        <div class="order-item__group">
          <span class="order-item__title">Способ доставки</span>
          <span class="order-item__info">Самовывоз</span>
        </div>
        <div class="order-item__group">
          <span class="order-item__title">Способ оплаты</span>
          <span class="order-item__info">Наличными</span>
        </div>
        <div class="order-item__group order-item__group--status">
          <span class="order-item__title">Статус заказа</span>
          <span class="order-item__info order-item__info--no">Не выполнено</span>
          <button class="order-item__btn">Изменить</button>
        </div>
      </div>
      <div class="order-item__wrapper">
        <div class="order-item__group">
          <span class="order-item__title">Адрес доставки</span>
          <span class="order-item__info">г. Москва, ул. Пушкина, д.5, кв. 233</span>
        </div>
      </div>
      <div class="order-item__wrapper">
        <div class="order-item__group">
          <span class="order-item__title">Комментарий к заказу</span>
          <span class="order-item__info">Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Вдали от всех живут они в буквенных домах на берегу.</span>
        </div>
      </div>
    </li>
<?php
while ($order = $stmt->fetch(PDO::FETCH_LAZY))
{
  // echo '<pre>';
  // print_r($order);
  // echo '</pre>';
?>
    <li class="order-item page-order__item">
      <div class="order-item__wrapper">
        <div class="order-item__group order-item__group--id" style="display: grid; grid-template-columns: 50% 50%">
          <div>
            <span class="order-item__title">Номер заказа</span>
            <span class="order-item__info order-item__info--id"><?= $order['id'] ?></span>
          </div>
          <div>
            <span class="order-item__title">Номер товараа</span>
            <span class="order-item__info order-item__info--id"><?= $order['product'] ?></span>
          </div>
        </div>
        <div class="order-item__group">
          <span class="order-item__title">Сумма заказа</span>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/get_fine_price_str.php');
echo  getFinePrice($order['sum']);
?> руб.
        </div>
        <button class="order-item__toggle"></button>
      </div>
      <div class="order-item__wrapper">
        <div class="order-item__group order-item__group--margin">
          <span class="order-item__title">Заказчик</span>
          <span class="order-item__info"><?= $order['customer']?></span>
        </div>
        <div class="order-item__group">
          <span class="order-item__title">Номер телефона</span>
          <span class="order-item__info"><?= $order['phone'] ?></span>
        </div>
        <div class="order-item__group">
          <span class="order-item__title">Способ доставки</span>
          <span class="order-item__info">
<?php
if ($order['delivery'] === 'dev-yes') {
  echo 'Курьерская доставка';
} elseif ($order['delivery'] === 'dev-no') {
  echo 'Самовывоз';
}  
?></span>
        </div>
        <div class="order-item__group">
          <span class="order-item__title">Способ оплаты</span>
          <span class="order-item__info">
<?php 
if ($order['payment'] === 'cash') {
  echo 'Наличными';
} elseif ($order['payment'] === 'card') {
  echo 'Банковской картой';
}
?></span>
        </div>
        <div class="order-item__group order-item__group--status">
          <span class="order-item__title">Статус заказа</span>
          <span class="order-item__info 
<?php
if ($order['status'] === 0) {
  echo 'order-item__info--no';
} elseif ($order['status'] === 1) {
  echo 'order-item__info--yes';
}
?>">
<?php
if ($order['status'] === 0) {
  echo 'Не выполнено';
} elseif ($order['status'] === 1) {
  echo 'Выполнено';
}
?></span>
          <!-- <button class="order&#45;item__btn">Изменить</button> -->
          <a href="<?= $_SERVER['PHP_SELF'] ?>?done&id=<?= $order['id'] ?>" class="order-item__btn" >Изменить</a>
        </div>
      </div>
      <div class="order-item__wrapper">
        <div class="order-item__group">
          <span class="order-item__title">Адрес доставки</span>
          <span class="order-item__info"><?= $order['address'] ?></span>
        </div>
      </div>
      <div class="order-item__wrapper">
        <div class="order-item__group">
          <span class="order-item__title">Комментарий к заказу</span>
          <span class="order-item__info"><?= $order['comment']?></span>
        </div>
      </div>
    </li>
<?php
}
?>
  </ul>
</main>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . ('/templates/footer.php');
?>
</body>
</html>
