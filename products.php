<?php
require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/check_authorized.php');
checkAuth();

require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/db_connect.php');
$pdo = getDbConnect();

require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/get_user_role.php');
$authUserGroups = getUserGroups($pdo, $_COOKIE['auth_person']);
// echo '<pre>';
// var_dump($authUserGroups);
// echo '</pre>';

//если пользователь не админ отправляем его на список заказов
if (array_search('admin', $authUserGroups) === false) {
  echo "<script>alert('Только для администраторов!')</script>"; 

  header('Refresh: 0.5; URL=/orders.php');
} else {
  //обработка запроса на удаление товара
  if (isset($_GET['del'])) {
    // echo 'Запрос на удаление товара ' . $_GET['id'];
    //получаем имя картинки удаляемого товара
    $stmt = $pdo -> prepare("
      SELECT photo 
      FROM product
      WHERE id = :id");
    
    $stmt -> execute(['id' => $_GET['id']]); 
    $imgForDel = $stmt->fetch(PDO::FETCH_LAZY);
    var_dump($imgForDel);
    //удаляем данные из БД
    $stmt = $pdo -> prepare("
      DELETE FROM product
      WHERE id = :id");

    $stmt -> execute(['id' => $_GET['id']]); 
    $stmt->fetch(PDO::FETCH_LAZY);
    //удаляем картинку
    $imgsDir = $_SERVER['DOCUMENT_ROOT'] . '/img/products/';
    unlink($imgsDir . $imgForDel['photo']);
    header('Refresh: 0.5; URL=/products.php');
  }
  //запрос списка товаров
  $stmt = $pdo -> query("SELECT * FROM product");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Товары</title>

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
<main class="page-products">
  <h1 class="h h--1">Товары</h1>
  <a class="page-products__button button" href="/add.php">Добавить товар</a>
  <div class="page-products__header" style="display: grid; grid-template-columns: 205px 145px 145px 145px 100px 100px 1fr 1fr;">
    <span class="page-products__header-field">Название товара</span>
    <span class="page-products__header-field">ID</span>
    <span class="page-products__header-field">Цена</span>
    <span class="page-products__header-field">Категория</span>
    <span class="page-products__header-field">Новинка</span>
    <span class="page-products__header-field">Sale</span>
  </div>
  <ul class="page-products__list">
    <!-- <li class="product&#45;item page&#45;products__item" style="display: grid; grid&#45;template&#45;columns: 205px 145px 145px 145px 100px 100px 1fr 1fr;"> -->
    <!--   <b class="product&#45;item__name">Туфли черные</b> -->
    <!--   <span class="product&#45;item__field">235454345</span> -->
    <!--   <span class="product&#45;item__field">2 500 руб.</span> -->
    <!--   <span class="product&#45;item__field"> -->
    <!--     <span>Женщины</span> -->
    <!--   </span> -->
    <!--   <span class="product&#45;item__field">Да</span> -->
    <!--   <span class="product&#45;item__field">Да</span> -->
    <!--   </span> -->
    <!--   <a href="add.php?edit&#38;id=111" class="product&#45;item__edit" aria&#45;label="Редактировать"></a> -->
    <!--   <button class="product&#45;item__delete"></button> -->
    <!-- </li> -->

<?php
  while ($product = $stmt -> fetch(PDO::FETCH_LAZY)) {
    // echo '<pre>';
    // print_r($product);
    // echo '</pre>';
?>
    <li class="product-item page-products__item" style="display: grid; grid-template-columns: 205px 145px 145px 145px 100px 100px 1fr 1fr;">
    <b class="product-item__name"><?= $product['name'] ?></b>
    <span class="product-item__field"><?= $product['id'] ?></span>
    <span class="product-item__field">
<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/get_fine_price_str.php');
    echo getFinePrice($product['price']);
?> руб.</span>
      <!-- <span class="product&#45;item__field">Женщины</span> -->
    <span class="product-item__field">
<?php
    $stmt3 = $pdo -> prepare("
      SELECT product_id, section_id, name
      FROM product_sections 
      LEFT JOIN section ON section.id = section_id 
      WHERE product_id = :product_id
      ");
    $stmt3->execute(['product_id' => $product['id']]);

    while ($prodSec = $stmt3 -> fetch(PDO::FETCH_LAZY)) {
?>    
      <span class="product-item__field">
<?php 
     switch($prodSec['name']) {
       case 'womens':
        echo 'Женщины';
       break; 
       case 'mens':
        echo 'Мужчины';
       break; 
       case 'childish':
        echo 'Дети';
       break; 
       case 'accessories':
        echo 'Аксессуары';
       break;
       default:
        echo '';
       break;
     }
?></span>
<?php
    }
?>
    </span>
<?php
    $stmt2 = $pdo -> prepare("
      SELECT product_id, subsection_id, name
      FROM product_subsections 
      LEFT JOIN subsection ON subsection.id = subsection_id 
      WHERE product_id = :product_id
      ORDER BY subsection.name ASC
      ");
    $stmt2->execute(['product_id' => $product['id']]);

    $prodSubsecArr = [];
    while ($prodSubsec = $stmt2 -> fetch(PDO::FETCH_LAZY)) {
      // var_dump($prodSubsec['name']);
      array_push($prodSubsecArr, $prodSubsec['name']);
    }
?>    
    <span class="product-item__field">
<?php
     if (array_search('new', $prodSubsecArr, $strict = false) !== false) { 
       echo 'Да';
     } else {
       echo 'Нет';
     }
?></span>
    
    <span class="product-item__field">
<?php
     if (array_search('sale', $prodSubsecArr, $strict = false) !== false) { 
       echo 'Да';
     } else {
       echo 'Нет';
     }
?></span>
      <!-- <span class="product&#45;item__field">Да</span> -->
      <a href="/add.php?edit&id=<?= $product['id'] ?>" class="product-item__edit" aria-label="Редактировать"></a>
      <!-- <button class="product&#45;item__delete"></button> -->
      <a href="<?= $_SERVER['PHP_SELF'] ?>?del&id=<?= $product['id'] ?>" class="product-item__delete" aria-label="Удалить"></a>
    </li>
<?php
  }
}
?>
  </ul>
</main>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . ('/templates/footer.php');
?>
</body>
</html>
