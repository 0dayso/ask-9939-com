<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require("../config.php");

$__DBHost		= "192.168.1.99";  //read
$__DBUser		= "9939_com_v2sns";
$__DBPwd        = "snsrewou#*&#inewk";
$__DBName		= "9939_com_v2sns";


$connect = mysql_connect($__DBHost,$__DBUser,$__DBPwd);

$result=mysql_query("SHOW PROCESSLIST",$connect);
$i = 1;
while($proc=mysql_fetch_assoc($result)){

	//print_r($proc);
    //if($proc["Command"]=="Sleep"  && $proc["Time"]>120){
      //if($proc["User"]<>"system user" && $proc["User"]<>"xzx"){
		//if($proc["Command"]=="Sleep"){
		if($proc["State"]=="Locked"){
         echo $i;
         $i++;
          if(mysql_query("kill ".$proc["Id"],$connect)){
              echo "ok<br>";
          }
          else{
              echo "<font color=red>not ok</font><br>";
          }
    }
}

?>
