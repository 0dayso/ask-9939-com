<?php
/**
 * @version 0.0.0.1
 */



$arr = [3, 2];

array_walk($arr, function (&$val, $key){
    $val += 2;
});

print_r($arr);
