<?php
function getDbConnect() {
  require_once $_SERVER['DOCUMENT_ROOT'] . ('/data/db_params.php');

  // подсоединяемся к БД c проверкой соединения
  try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword);
  } catch (PDOException $e) {
    print "Error!: " . $e->getMessage();
    die();
  }

  return $pdo;
}