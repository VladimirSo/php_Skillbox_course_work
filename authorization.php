<?php
session_start();

//авторизация
if (isset($_POST['login'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

  $login = $_POST['login'];
  $pass = $_POST['password'];

  require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/db_connect.php');
  $pdo = getDbConnect();

  $stmt = $pdo -> prepare("SELECT email, password FROM user WHERE email = :login");

  $stmt->execute(['login' => $login]);
  $authData = $stmt->fetch(PDO::FETCH_LAZY);
  $pdo = null;

  if ( isset($authData['email']) ) {
    $hash = $authData['password'];

    if (password_verify($pass, $hash)) {
      // echo $login . ' - AUTH YES!';
      setcookie('auth_person', $login, time()+3600*24*30, '/');
      $_SESSION['is_authorized'] = true;
      //авторизованного пользователя отправляем на список заказов
      header('Location: /orders.php');
    }
  }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Авторизация</title>

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
require_once $_SERVER['DOCUMENT_ROOT'] . ('/templates/main_header.php');
?>
<main class="page-authorization">
  <h1 class="h h--1">Авторизация</h1>
  <form class="custom-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
    <input type="email" class="custom-form__input" name="login" required="">
    <input type="password" class="custom-form__input" name="password" required="">
    <button class="button" type="submit">Войти в личный кабинет</button>
  </form>
</main>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . ('/templates/footer.php');
?>
</body>
</html>
