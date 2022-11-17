<?php
error_reporting(E_ALL);
session_start();

echo '<pre>';
var_dump($_POST);
var_dump($_GET);
echo '</pre>';

var_dump(!isset($_GET['filter-products']));

$sendOrderErr = '';

if (isset($_POST['send_order'])) {
  foreach($_POST as $key => $val) {
   if ( ($key === 'surname' && $val === '') || 
     ($key === 'name' && $val === '') ||
     ($key === 'phone' && $val === '') ||
     ($key === 'email' && $val === '') 
   ) {
     $sendOrderErr = 'Форма не валидна!';
   }

   if ($key === 'delivery' && $val === 'dev-yes') {
    foreach($_POST as $key => $value) {
      if ( ($key === 'city' && $value === '') ||
        ($key === 'street' && $value === '') ||
        ($key === 'home' && $value === '') ||
        ($key === 'aprt' && $value === '')
      ) {
        $sendOrderErr = 'Форма не валидна!';
      }
    }
   }
  }

  echo $sendOrderErr;
}

if (isset($_GET['filter-sent'])) {
 echo 'Есть ГЕТ-запрос фильтра товаров: ';
//  var_dump($_GET);
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Fashion</title>

  <meta name="description" content="Fashion - интернет-магазин">
  <meta name="keywords" content="Fashion, интернет-магазин, одежда, аксессуары">

  <meta name="theme-color" content="#393939">

  <link rel="preload" href="img/intro/coats-2018.jpg" as="image">
  <link rel="preload" href="fonts/opensans-400-normal.woff2" as="font">
  <link rel="preload" href="fonts/roboto-400-normal.woff2" as="font">
  <link rel="preload" href="fonts/roboto-700-normal.woff2" as="font">

  <link rel="icon" href="img/favicon.png">
  <link rel="stylesheet" href="css/style.min.css">

  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="js/scripts.js" defer=""></script>
</head>
<body>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . ('/templates/main_header.php');
?>
<main class="shop-page">
  <header class="intro">
    <div class="intro__wrapper">
      <h1 class=" intro__title">COATS</h1>
      <p class="intro__info">Collection 2018</p>
    </div>
  </header>
  <section class="shop container">
    <section class="shop__filter filter">
      <form class="filter-form js-filter-form" name="prodsSelect" id="prodsSelect" action="<?= $_SERVER['PHP_SELF'] ?>" method="GET">
        <div class="filter__wrapper">
          <b class="filter__title">Категории</b>
          <ul class="filter__list">
            <li>
              <a class="filter__list-item js-form-link 
<?php 
if (!isset($_GET['filter-products'])) {
  echo 'active';
} elseif ($_GET['filter-products'] === 'all') {
  echo 'active';
} else {
  echo '';
}
?>" href="/?filter-products=all">Все</a>
            </li>
            <li>
              <a class="filter__list-item js-form-link <?= isset($_GET['filter-products']) && $_GET['filter-products'] === 'womens' ? 'active' : ''; ?>" href="/?filter-products=womens">Женщины</a>
            </li>
            <li>
              <a class="filter__list-item js-form-link <?= isset($_GET['filter-products']) && $_GET['filter-products'] === 'mens' ? 'active' : ''; ?>" href="/?filter-products=mens">Мужчины</a>
            </li>
            <li>
              <a class="filter__list-item js-form-link <?= isset($_GET['filter-products']) && $_GET['filter-products'] === 'childish' ? 'active' : ''; ?>" href="/?filter-products=childish">Дети</a>
            </li>
            <li>
              <a class="filter__list-item js-form-link <?= isset($_GET['filter-products']) && $_GET['filter-products'] === 'accessories' ? 'active' : ''; ?>" href="/?filter-products=accessories">Аксессуары</a>
            </li>
          </ul>
        </div>

        <div class="filter__wrapper">
          <b class="filter__title">Фильтры</b>
          <div class="filter__range range">
            <span class="range__info">Цена</span>
            <div class="range__line" aria-label="Range Line"></div>
            <div class="range__res">
              <span class="range__res-item min-price" name="filter-min-price" form="prodsSelect">

<?php 
if (isset($_GET['filter-min-price'])) {
  echo $_GET['filter-min-price'] . ' руб.';
} else {
  echo '350 руб.';
}
?></span>
              <span class="range__res-item max-price" name="filter-max-price" form="prodsSelect">
<?php 
if (isset($_GET['filter-max-price'])) {
  echo $_GET['filter-max-price'] . ' руб.';
} else {
  echo '32 000 руб.';
}
?></span>
            </div>
          </div>
        </div>

        <fieldset class="custom-form__group">
        <input type="checkbox" name="filter-new" id="new" class="custom-form__checkbox"  
<?php 
if (isset($_GET['filter-new']) && $_GET['filter-new'] === 'true') {
  echo 'checked';
} else {
  echo '';
}
?> >
          <label for="new" class="custom-form__checkbox-label custom-form__info" style="display: block;">Новинка</label>
          <input type="checkbox" name="filter-sale" id="sale" class="custom-form__checkbox" 
<?php 
if (isset($_GET['filter-sale']) && $_GET['filter-sale'] === 'true') {
  echo 'checked';
} else {
  echo '';
}
?> >
          <label for="sale" class="custom-form__checkbox-label custom-form__info" style="display: block;">Распродажа</label>
        </fieldset>

        <button class="button" type="submit" name="filter-sent" value="true" style="width: 100%">Применить</button>
      </form>
    </section>

    <div class="shop__wrapper">
      <section class="shop__sorting">
        <div class="shop__sorting-item custom-form__select-wrapper">
          <select class="custom-form__select" name="filter-sort-category" form="prodsSelect">
            <option hidden="" selected value="<?= (isset($_GET['filter-sort-category'])) ? $_GET['filter-sort-category'] : 'no'?>">
<?php 
if (isset($_GET['filter-sort-category']) && $_GET['filter-sort-category'] === 'price') {
  echo 'По цене';
} elseif (isset($_GET['filter-sort-category']) && $_GET['filter-sort-category'] === 'name') {
  echo 'По названию';
} else {
  echo 'Сортировка';
}
?></option>
            <option value="price">По цене</option>
            <option value="name">По названию</option>
          </select>
        </div>
        <div class="shop__sorting-item custom-form__select-wrapper">
          <select class="custom-form__select" name="filter-sort-order" form="prodsSelect">
            <option hidden="" selected value="<?= (isset($_GET['filter-sort-order'])) ? $_GET['filter-sort-order'] : 'no'?>">
<?php
if (isset($_GET['filter-sort-order']) && $_GET['filter-sort-order'] === 'asc') {
  echo 'По возрастанию';
} elseif (isset($_GET['filter-sort-category']) && $_GET['filter-sort-order'] === 'desc') {
  echo 'По убыванию';
} else {
  echo 'Порядок';
}
?></option>
            <option value="asc">По возрастанию</option>
            <option value="desc">По убыванию</option>
          </select>
        </div>
        <p class="shop__sorting-res">Найдено <span class="res-sort">858</span> моделей</p>
      </section>
      <section class="shop__list">
        <article class="shop__item product" tabindex="0">
          <span class="product__id" hidden>1236568434</span>
          <div class="product__image">
            <img src="img/products/product-1.jpg" alt="product-name">
          </div>
          <p class="product__name">Платье со складками</p>
          <span class="product__price">2 999 руб.</span>
        </article>
        <article class="shop__item product" tabindex="0">
          <div class="product__image">
            <img src="img/products/product-2.jpg" alt="product-name">
          </div>
          <p class="product__name">Платье со складками</p>
          <span class="product__price">2 999 руб.</span>
        </article>
        <article class="shop__item product" tabindex="0">
          <span class="product__id" hidden>25</span>
          <div class="product__image">
            <img src="img/products/product-3.jpg" alt="product-name">
          </div>
          <p class="product__name">Платье со складками</p>
          <span class="product__price">2 999 руб.</span>
        </article>
        <article class="shop__item product" tabindex="0">
          <div class="product__image">
            <img src="img/products/product-4.jpg" alt="product-name">
          </div>
          <p class="product__name">Платье со складками</p>
          <span class="product__price">2 999 руб.</span>
        </article>
        <article class="shop__item product" tabindex="0">
          <div class="product__image">
            <img src="img/products/product-5.jpg" alt="product-name">
          </div>
          <p class="product__name">Платье со складками</p>
          <span class="product__price">2 999 руб.</span>
        </article>
        <article class="shop__item product" tabindex="0">
          <div class="product__image">
            <img src="img/products/product-6.jpg" alt="product-name">
          </div>
          <p class="product__name">Платье со складками</p>
          <span class="product__price">2 999 руб.</span>
        </article>
        <article class="shop__item product" tabindex="0">
          <div class="product__image">
            <img src="img/products/product-7.jpg" alt="product-name">
          </div>
          <p class="product__name">Платье со складками</p>
          <span class="product__price">2 999 руб.</span>
        </article>
        <article class="shop__item product" tabindex="0">
          <div class="product__image">
            <img src="img/products/product-8.jpg" alt="product-name">
          </div>
          <p class="product__name">Платье со складками</p>
          <span class="product__price">2 999 руб.</span>
        </article>
        <article class="shop__item product" tabindex="0">
          <div class="product__image">
            <img src="img/products/product-9.jpg" alt="product-name">
          </div>
          <p class="product__name">Платье со складками</p>
          <span class="product__price">2 999 руб.</span>
        </article>
      </section>
      <ul class="shop__paginator paginator">
        <li>
          <a class="paginator__item">1</a>
        </li>
        <li>
          <a class="paginator__item" href="">2</a>
        </li>
      </ul>
    </div>
  </section>
  <section class="shop-page__order" hidden="">
    <div class="shop-page__wrapper">
       <h2 class="h h--1">Оформление заказа</h2>
       <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data" class="custom-form js-order" id="order-form">
       <!-- <form action="/src/order_handler.php" method="POST" enctype="text/html" class="custom&#45;form js&#45;order" id="order&#45;form"> -->
        <fieldset class="custom-form__group">
          <legend class="custom-form__title">Укажите свои личные данные</legend>
          <p class="custom-form__info">
            <span class="req">*</span> поля обязательные для заполнения
          </p>
          <div class="custom-form__column">
            <label class="custom-form__input-wrapper" for="surname">
              <input id="surname" class="custom-form__input" type="text" name="surname" required="">
              <p class="custom-form__input-label">Фамилия <span class="req">*</span></p>
            </label>
            <label class="custom-form__input-wrapper" for="name">
              <input id="name" class="custom-form__input" type="text" name="name" required="">
              <p class="custom-form__input-label">Имя <span class="req">*</span></p>
            </label>
            <label class="custom-form__input-wrapper" for="thirdName">
              <input id="thirdName" class="custom-form__input" type="text" name="thirdName">
              <p class="custom-form__input-label">Отчество</p>
            </label>
            <label class="custom-form__input-wrapper" for="phone">
              <!-- <input id="phone" class="custom&#45;form__input" type="tel" name="thirdName" required=""> -->
              <input id="phone" class="custom-form__input" type="tel" name="phone" required="">
              <p class="custom-form__input-label">Телефон <span class="req">*</span></p>
            </label>
            <label class="custom-form__input-wrapper" for="email">
              <!-- <input id="email" class="custom&#45;form__input" type="email" name="thirdName" required=""> -->
              <input id="email" class="custom-form__input" type="email" name="email" required="">
              <p class="custom-form__input-label">Почта <span class="req">*</span></p>
            </label>
          </div>
        </fieldset>
        <fieldset class="custom-form__group js-radio">
          <legend class="custom-form__title custom-form__title--radio">Способ доставки</legend>
          <input id="dev-no" class="custom-form__radio" type="radio" name="delivery" value="dev-no" checked="">
          <label for="dev-no" class="custom-form__radio-label">Самовывоз</label>
          <input id="dev-yes" class="custom-form__radio" type="radio" name="delivery" value="dev-yes">
          <label for="dev-yes" class="custom-form__radio-label">Курьерная доставка</label>
        </fieldset>
        <div class="shop-page__delivery shop-page__delivery--no">
          <table class="custom-table">
            <caption class="custom-table__title">Пункт самовывоза</caption>
            <tr>
              <td class="custom-table__head">Адрес:</td>
              <td>Москва г, Тверская ул,<br> 4 Метро «Охотный ряд»</td>
            </tr>
            <tr>
              <td class="custom-table__head">Время работы:</td>
              <td>пн-вс 09:00-22:00</td>
            </tr>
            <tr>
              <td class="custom-table__head">Оплата:</td>
              <td>Наличными или банковской картой</td>
            </tr>
            <tr>
              <td class="custom-table__head">Срок доставки: </td>
              <td class="date">13 декабря—15 декабря</td>
            </tr>
          </table>
        </div>
        <div class="shop-page__delivery shop-page__delivery--yes" hidden="">
          <fieldset class="custom-form__group">
            <legend class="custom-form__title">Адрес</legend>
            <p class="custom-form__info">
              <span class="req">*</span> поля обязательные для заполнения
            </p>
            <div class="custom-form__row">
              <label class="custom-form__input-wrapper" for="city">
                <input id="city" class="custom-form__input" type="text" name="city">
                <p class="custom-form__input-label">Город <span class="req">*</span></p>
              </label>
              <label class="custom-form__input-wrapper" for="street">
                <input id="street" class="custom-form__input" type="text" name="street">
                <p class="custom-form__input-label">Улица <span class="req">*</span></p>
              </label>
              <label class="custom-form__input-wrapper" for="home">
                <input id="home" class="custom-form__input custom-form__input--small" type="text" name="home">
                <p class="custom-form__input-label">Дом <span class="req">*</span></p>
              </label>
              <label class="custom-form__input-wrapper" for="aprt">
                <input id="aprt" class="custom-form__input custom-form__input--small" type="text" name="aprt">
                <p class="custom-form__input-label">Квартира <span class="req">*</span></p>
              </label>
            </div>
          </fieldset>
        </div>
        <fieldset class="custom-form__group shop-page__pay">
          <legend class="custom-form__title custom-form__title--radio">Способ оплаты</legend>
          <input id="cash" class="custom-form__radio" type="radio" name="pay" value="cash">
          <label for="cash" class="custom-form__radio-label">Наличные</label>
          <input id="card" class="custom-form__radio" type="radio" name="pay" value="card" checked="">
          <label for="card" class="custom-form__radio-label">Банковской картой</label>
        </fieldset>
        <fieldset class="custom-form__group shop-page__comment">
          <legend class="custom-form__title custom-form__title--comment">Комментарии к заказу</legend>
          <textarea class="custom-form__textarea" name="comment"></textarea>
        </fieldset>
        <input class="js-product-id" name="product-id" type="hidden" value="">
        <button class="button" type="submit" name="order">Отправить заказ</button>
      </form>
    </div>
  </section>
  <section class="shop-page__popup-end" hidden="">
    <div class="shop-page__wrapper shop-page__wrapper--popup-end">
      <h2 class="h h--1 h--icon shop-page__end-title">Спасибо за заказ!</h2>
      <p class="shop-page__end-message">Ваш заказ успешно оформлен, с вами свяжутся в ближайшее время</p>
      <button class="button">Продолжить покупки</button>
    </div>
  </section>
</main>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . ('/templates/footer.php');
?>
</body>
</html>
