<?php
session_start();
date_default_timezone_set ('Asia/Shanghai');
//if( isset($_SESSION['last_key']) ) header("Location: weibolist.php");
include_once( 'config.php' );
include_once( 'weibooauth.php' );

$o = new WeiboOAuth( WB_AKEY , WB_SKEY  );
$keys = $o->getRequestToken();
$aurl = $o->getAuthorizeURL( $keys['oauth_token'] ,false , 'http://ask.9939.com/weibo/callback.php');

$_SESSION['keys'] = $keys;

?>
<a href="<?=$aurl?>">Use Oauth to login</a>