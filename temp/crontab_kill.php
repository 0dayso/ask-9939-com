<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//require("../config.php");

$__DBHost		= "192.168.1.99";  //read
$__DBUser		= "9939_com_v2sns";
$__DBPwd        = "snsrewou#*&#inewk";
$__DBName		= "9939_com_v2sns";

$__DBHost		= "192.168.1.99";  
$__DBUser		= "xiongzhixin";
$__DBPwd        = "xiong123root";
//$__DBName		= "9939_com_v2sns";



$connect = mysql_connect($__DBHost,$__DBUser,$__DBPwd);

$result=mysql_query("SHOW PROCESSLIST",$connect);
$i = 1;

while($proc=mysql_fetch_assoc($result)){
  $aList[] = $proc;
}

print_r($aList);


/**

 if(mysql_query("kill ".$proc["Id"],$connect)){
	  echo "ok<br>";
  }
  else{
	  echo "<font color=red>not ok</font><br>";
  }
  **/
?>
