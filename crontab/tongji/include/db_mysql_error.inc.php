<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

$timestamp = time();
$errmsg = '';

$dberror = $this->error();
$dberrno = $this->errno();
if($dberrno == 1114) {
	E(1114,9);
} else {
	if($message) {
		$errmsg = "info: $message ";
	}
	$errmsg .= "Time: ".gmdate("Y-n-j g:ia", $timestamp + ($GLOBALS['timeoffset'] * 3600))." ";
	$errmsg .= "Script: ".$GLOBALS['PHP_SELF']." ";
	if($sql) {
		$errmsg .= "SQL: ".htmlspecialchars($sql)." ";
	}
	$errmsg .= "Error:  $dberror ";
	$errmsg .= "Errno.:  $dberrno";
	E($errmsg,9);
}
?>