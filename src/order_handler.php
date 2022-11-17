<?php

echo '<pre>';
var_dump($_POST);
echo '</pre>';
if (! empty($_POST)) {
  $surName = $_POST['surname'];
  $name = $_POST['name'];
  $thirdName = isset($_POST['thirdName']) ? $_POST['thirdName'] : '';
  $phone = $_POST['phone'];
  $email = $_POST['email'];
  $delivery = $_POST['delivery'];
  $city = isset($_POST['city']) ? $_POST['city'] : '';
  $street = isset($_POST['street']) ? $_POST['street'] : '';
  $home = isset($_POST['home']) ? $_POST['home'] : '';
  $aprt = isset($_POST['aprt']) ? $_POST['aprt'] : '';
  $pay = $_POST['pay'];
  $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
  $productId = $_POST['product-id'];
}

require_once ($_SERVER['DOCUMENT_ROOT'] . '/data/shipping_prices.php');
$shippCost = SHIPPING_COST;
$freeDelivery = ORDERS_COST_FOR_FREE_DELIVERY;
$orderPrice = 0;
require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/db_connect.php');
$pdo = getDbConnect();

$stmt = $pdo -> prepare("SELECT * FROM product WHERE id = :id");
$stmt->execute(['id' => $productId]);
$prod = $stmt->fetch(PDO::FETCH_LAZY);
// var_dump($prod['price']);
require_once $_SERVER['DOCUMENT_ROOT'] . ('/src/get_num_from_str.php');
$productPrice = getNumFromStr($prod['price']);
var_dump($productPrice);
// //
if ($delivery === 'dev-yes' && $productPrice < $freeDelivery) {
   $orderPrice = $productPrice + $shippCost; 
} else {
   $orderPrice = $productPrice; 
}
var_dump($orderPrice);
//
$stmt = $pdo -> prepare("
  INSERT ordering 
  SET sum = :sum, customer = :customer, phone = :phone, email = :email, delivery = :delivery, payment = :payment, address = :address, comment = :comment, product = :product");

$customer = $surName . ' ' . $name . ' ' . $thirdName;
$address = $delivery === 'dev-yes' ? 'г.' . $city . ', ул.' . $street . ', д.' . $home . ', кв.' . $aprt : '';

$stmt->execute(['sum' => $orderPrice, 'customer' => $customer, 'phone' => $phone, 'email' => $email, 'delivery' => $delivery, 'payment' => $pay, 'address' => $address, 'comment' => $comment, 'product' => $productId]);
$stmt->fetch(PDO::FETCH_LAZY);
//
