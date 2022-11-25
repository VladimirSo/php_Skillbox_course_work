<?php
require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/check_authorized.php');
checkAuth();

require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/db_connect.php');
$pdo = getDbConnect();

require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/get_user_role.php');
$authUserGroups = getUserGroups($pdo, $_COOKIE['auth_person']);
echo '<pre>';
var_dump($authUserGroups);
echo '</pre>';
//если пользователь не админ отправляем его на список заказов
if (array_search('admin', $authUserGroups) === false) {
  echo "<script>alert('Только для администраторов!')</script>"; 

  header('Refresh: 0.5; URL=/orders.php');
}

var_dump($_SERVER['PHP_SELF']);

if (! empty($_POST)) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
}

if (! empty($_FILES)) {
  echo '<pre>';
  var_dump($_FILES);
  echo '</pre>';
}

$prodName = isset($_POST['product-name']) ? $_POST['product-name'] : '';
$prodPrice = isset($_POST['product-price']) ? $_POST['product-price'] : '';
$prodCategory = isset($_POST['category']) ? $_POST['category'] : [];
$prodNew = isset($_POST['new']) && $_POST['new'] === 'on'  ? 'new' : '';
$prodSale = isset($_POST['sale']) && $_POST['sale'] === 'on'  ? 'sale' : '';
$prodFoto = isset($_FILES['product-foto']) ? $_FILES['product-foto']['full_path'] : '';

if (isset($_POST['product-name']) && isset($_POST['product-price'])) {
  if (isset($_FILES['product-foto'])) {
   // echo "<script>alert('Товар добавлен')</script>";
    // товар первоначально добавляемый в БД еще не миеет id, поэтому
    // если с POST-запросом был получен id товара, то это обновление
    // данных, иначе - добавление нового товара
    if (isset($_POST['product-id'])) {
      $stmt = $pdo -> prepare("
        UPDATE product
        SET name = :name, price = :price, photo = :photo
        WHERE id = :id"); 
    } else {
      $stmt = $pdo -> prepare("
        INSERT INTO product 
        SET name = :name, price = :price, photo = :photo");
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/get_num_from_str.php');
    $price = getNumFromStr($prodPrice);
    //добавляем в БД обязательные данные товара
    $exArr = ['name' => $prodName, 'price' => $price, 'photo' => $prodFoto];

    if (isset($_POST['product-id'])) {
     $exArr = array_merge($exArr, array('id' => $_POST['product-id']));
     var_dump(array_merge($exArr, array('id' => $_POST['product-id'])));
    }
    var_dump($exArr);
    $stmt->execute($exArr);
    $stmt->fetch(PDO::FETCH_LAZY);
    //загружаем картинку для товара
    $uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/upload/';
    move_uploaded_file($_FILES['product-foto']['tmp_name'], $uploadPath . $_FILES['product-foto']['name']);
    //если есть необязательные параметры для товара заносим в БД их
    //если это обновление данных для товара, то сначала удаляем старые 
    //данные
    if (isset($_POST['product-id'])) {
      $stmt = $pdo -> prepare("
        DELETE
        FROM product_sections
        WHERE product_id = :product_id");
      
      $stmt->execute(['product_id' => $_POST['product-id']]);
      $stmt->fetch(PDO::FETCH_LAZY);
 
      $stmt = $pdo -> prepare("
        DELETE
        FROM product_subsections
        WHERE product_id = :product_id");
      
      $stmt->execute(['product_id' => $_POST['product-id']]);
      $stmt->fetch(PDO::FETCH_LAZY);
    }

    if (isset($_POST['category']) || isset($_POST['new']) || isset($_POST['sale'])) {
      $stmt = $pdo -> prepare("
        SELECT id 
        FROM product
        WHERE name = :name AND photo = :photo");

      $stmt->execute(['name' => $prodName, 'photo' => $prodFoto]);
      $prodId = ($stmt->fetch(PDO::FETCH_LAZY))['id'];
      // echo 'prod Id: ';
      // print_r($prodId);

      function addSubSection($pdo, $prodSubSec, $prodId) {
        $stmt = $pdo -> prepare("
          SELECT id 
          FROM subsection 
          WHERE name = :name");
        
        $stmt->execute(['name' => $prodSubSec]);
        $subSecId = ($stmt->fetch(PDO::FETCH_LAZY))['id'];

        $stmt = $pdo -> prepare("
          INSERT INTO product_subsections (product_id, subsection_id) 
          VALUES (:product_id, :subsection_id)");
        
        $stmt->execute(['product_id' => $prodId, 'subsection_id' => $subSecId]);
        $stmt->fetch(PDO::FETCH_LAZY);
      }

      if (isset($_POST['new'])) {
        addSubSection($pdo, $prodNew, $prodId);
      }

      if (isset($_POST['sale'])) {
        addSubSection($pdo, $prodSale, $prodId);
      }

      if (isset($_POST['category'])) {
        foreach ($prodCategory as &$value) {
          $stmt = $pdo -> prepare("
            SELECT id FROM section
            WHERE name = :name");

          $stmt->execute(['name' => $value]);
          $secId = ($stmt->fetch(PDO::FETCH_LAZY))['id'];

          $stmt = $pdo -> prepare("
            INSERT INTO product_sections (product_id, section_id) 
            VALUES (:product_id, :section_id)");
          
          $stmt->execute(['product_id' => $prodId, 'section_id' => $secId]);
          $stmt->fetch(PDO::FETCH_LAZY);
        }
      }
    }

    $pdo = null;
    // header('Refresh: 0.5; URL=/products.php');
  }
} elseif (!empty($_FILES)) {
  echo "<script>alert('Заполните поля \"Данные о товаре\" и \"Фотография товара\"!')</script>";
}

if (isset($_GET['edit'])) {
  echo 'Запрос на редактирование товара ' . $_GET['id'];

  $stmt = $pdo -> prepare("
    SELECT id, name, price, photo 
    FROM product 
    WHERE id = :id");

  $stmt->execute(['id' => $_GET['id']]); 
  $edProd = $stmt->fetch(PDO::FETCH_LAZY);
  var_dump($edProd);

  $stmt2 = $pdo -> prepare("
    SELECT product.id, product_id, section_id, section.name
    FROM product
    LEFT JOIN product_sections ON product_id = product.id
    LEFT JOIN section ON section.id = section_id
    WHERE product.id = :id");
  $stmt2->execute(['id' => $_GET['id']]);
  $edProdSecArr = []; 
  while ($prodSec = $stmt2 -> fetch(PDO::FETCH_LAZY)) {
    var_dump($prodSec['name']);
    array_push($edProdSecArr, $prodSec['name']);
  }
  var_dump($edProdSecArr);

  $stmt3 = $pdo -> prepare("
    SELECT product.id, product_id, subsection_id, subsection.name
    FROM product
    LEFT JOIN product_subsections ON product_id = product.id
    LEFT JOIN subsection ON subsection.id = subsection_id
    WHERE product.id = :id");
  $stmt3->execute(['id' => $_GET['id']]);
  $edProdSubsecArr = []; 
  while ($prodSubsec = $stmt3 -> fetch(PDO::FETCH_LAZY)) {
    var_dump($prodSubsec['name']);
    array_push($edProdSubsecArr, $prodSubsec['name']);
  }
  var_dump($edProdSubsecArr);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Добавление товара</title>

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
<main class="page-add">
  <h1 class="h h--1">Добавление товара</h1>
  <form class="custom-form js-add-product" action="<?= $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data" method="post">
    <fieldset class="page-add__group custom-form__group">
      <legend class="page-add__small-title custom-form__title">Данные о товаре</legend>
      <label for="product-name" class="custom-form__input-wrapper page-add__first-wrapper">
        <input type="text" class="custom-form__input" name="product-name" id="product-name" required value="
<?= isset($_GET['edit']) ? $edProd['name'] : '';?>">
        <p class="custom-form__input-label">
<?= isset($_GET['edit']) ? '' : 'Название товара'; ?>
          <!-- Название товара -->
        </p>
      </label>
      <label for="product-price" class="custom-form__input-wrapper">
        <input type="text" class="custom-form__input" name="product-price" id="product-price" required value="
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/get_fine_price_str.php');
if (isset($_GET['edit'])) {
  echo getFinePrice($edProd['price']);
} else {
 echo '';
}
?>">
        <p class="custom-form__input-label">
<?php 
if (isset($_GET['edit'])) {
  echo '';
} else {
  echo 'Цена товара';
}
?>
          <!-- Цена товара -->
        </p>
      </label>
    </fieldset>
    <fieldset class="page-add__group custom-form__group">
      <legend class="page-add__small-title custom-form__title">Фотография товара</legend>

<?php
if (isset($_GET['edit'])) {
?>
<div class="add-list__item" style="display: flex; flex-direction: column">
  <p style="margin: 0">Текущее фото:</p>
  <img src="/img/products/<?= $edProd['photo']?>" style="">
</div>
<?php
}
?>
      <ul class="add-list">
        <li class="add-list__item add-list__item--add">
          <input type="file" name="product-foto" id="product-photo" hidden="" required >
          <label for="product-photo">Добавить фотографию</label>
        </li>
      </ul>
    </fieldset>
    <fieldset class="page-add__group custom-form__group">
      <legend class="page-add__small-title custom-form__title">Раздел</legend>
      <div class="page-add__select">
        <select name="category[]" class="custom-form__select" multiple="multiple">
          <option hidden="">Название раздела</option>
          <option value="womens" 
<?php
if (isset($_GET['edit']) && array_search('womens', $edProdSecArr, $strict = false) !== false) {
  echo 'selected';
}
?>>Женщины</option>
          <option value="mens" 
<?php
if (isset($_GET['edit']) && array_search('mens', $edProdSecArr, $strict = false) !== false) {
  echo 'selected';
}
?>>Мужчины</option>
          <option value="childish" 
<?php
if (isset($_GET['edit']) && array_search('childish', $edProdSecArr, $strict = false) !== false) {
  echo 'selected';
}
?>>Дети</option>
          <option value="accessories" 
<?php
if (isset($_GET['edit']) && array_search('accessories', $edProdSecArr, $strict = false) !== false) {
  echo 'selected';
}
?>>Аксессуары</option>
        </select>
      </div>

      <input type="checkbox" name="new" id="new" class="custom-form__checkbox"
<?php
if (isset($_GET['edit']) && array_search('new', $edProdSubsecArr, $strict = false) !== false) {
  echo 'checked';
}
?>>
      <label for="new" class="custom-form__checkbox-label">Новинка</label>
      <input type="checkbox" name="sale" id="sale" class="custom-form__checkbox"
<?php
if (isset($_GET['edit']) && array_search('sale', $edProdSubsecArr, $strict = false) !== false) {
  echo 'checked';
}
?>>
      <label for="sale" class="custom-form__checkbox-label">Распродажа</label>
    </fieldset>
<?php
if (isset($_GET['edit'])) {
?>
      <input name="product-id" type="hidden" value="<?= $_GET['id'] ?>">
<?php
}
?>    <input class="button" type="submit" name="addProduct" value="Добавить товар">
  </form>
  <section class="shop-page__popup-end page-add__popup-end" hidden="">
    <div class="shop-page__wrapper shop-page__wrapper--popup-end">
      <h2 class="h h--1 h--icon shop-page__end-title">Товар успешно добавлен</h2>
    </div>
  </section>
</main>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . ('/templates/footer.php');
?>
</body>
</html>
