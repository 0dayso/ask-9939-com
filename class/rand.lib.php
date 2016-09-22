<?php
class rand{
	var $MODE_NUMERIC = 0;
	var $MODE_UPPERCASE = 1;
	var $MODE_LOWERCASE = 2;
	var $MODE_MIX_LETTER = 3; 
	var $MODE_MIX_NUMERIC_AND_lETTER = 4;
	
	function makeRand($iMode = 0, $iStrLen = NULL){
		switch ($iMode) {
			case MODE_NUMERIC:
				$a_array = range(0, 9);				
				break;
			case MODE_UPPERCASE:
				$a_array = range("A", "Z");
				break;
			case MODE_LOWERCASE:
				$a_array = range("a", "z");
				break;
			case MODE_MIX_LETTER:
				$a_array1 = range("A", "Z");
				$a_array2 = range("a", "z");
				$a_array = array_merge($a_array1, $a_array2);
				break;
			case 4:
				$a_array1 = range("A", "Z");
				$a_array2 = range("a", "z");
				$a_array3 = range(0, 9);
				$a_array = array_merge($a_array1, $a_array2, $a_array3);
				
				break;
			default:
				return NULL;
		}
		
		//print_r($a_array);
		
		srand ((float)microtime() * 1000000);
		shuffle($a_array);
		$s_str = implode("", $a_array);
		if (is_null($iStrLen)) {
			return $s_str;
		} else {
			return substr($s_str, 0, $iStrLen);
		}
	}

}
?>
