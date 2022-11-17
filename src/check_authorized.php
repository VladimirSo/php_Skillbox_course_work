<?php
function checkAuth()
{
  session_start();

  if (! isset($_SESSION['is_authorized'])) {
    header('Location: /index.php');
  } else {
    $login = $_COOKIE['auth_person'];

    setcookie('auth_person', $login, time()+3600*24*30, '/');
  }
} 
