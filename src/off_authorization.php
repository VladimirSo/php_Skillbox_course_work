<?php
session_start();
echo "<script>alert('Вы разавторизовались.')</script>";

session_destroy();
unset($_SESSION['is_authorized']);

header('Refresh: 0.5; URL=..');
