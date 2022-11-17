<?php

function getNumFromStr ($str) {
  $result = preg_replace('/[^0-9]/', '', $str);

  return $result; 
}


