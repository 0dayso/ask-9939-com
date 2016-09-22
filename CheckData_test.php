<?php

$str = "腾訊总公司";
require_once 'data/data_censor.php';

//print_r($_SGLOBAL);
foreach($_SGLOBAL['censor'] as $val) {
	//echo $val."<br>";
	if($val<>""){
		if(strstr($str,$val)!==false) {
			echo "ok";
		}
	}
}

?>