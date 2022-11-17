<?php
//преобразует строку с ценой в вид "12 345 678"
function getFinePrice ($value, $offset = 3) {
  $str = (string)$value;
  $l = strlen($str);
  $result = [];
  $k = 1;

  if ($l > 3) {
    for ($i = $l-1; $i >= 0; $i--) {
      $k++;

      if (($k % $offset) == 0) {
        $result[] = ' ';
      }

      $result[] = $str[$l-1 - $i];
    }
    return trim(implode(null, $result));
  } else {
    return $str;
  }
}

