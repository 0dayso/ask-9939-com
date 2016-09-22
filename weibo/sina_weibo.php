<?php
session_start();
date_default_timezone_set ('Asia/Shanghai');
include_once( 'config.php' );
include_once( 'weibooauth.php' );
$tj = $_GET['tj'];
if($tj=="tj_you"){
    $o = new WeiboOAuth( WB_AKEY , WB_SKEY  );
    $keys = $o->getRequestToken();
    $aurl = $o->getAuthorizeURL( $keys['oauth_token'] ,false , 'http://ask.9939.com/weibo/sina_weibo.php');
    $_SESSION['keys'] = $keys;
    header("Location:".$aurl);
}else{
    $o = new WeiboOAuth( WB_AKEY , WB_SKEY , $_SESSION['keys']['oauth_token'] , $_SESSION['keys']['oauth_token_secret']  );
    $last_key = $o->getAccessToken(  $_REQUEST['oauth_verifier'] ) ;
    $_SESSION['last_key'] = $last_key;

    $c = new WeiboClient( WB_AKEY , WB_SKEY , $_SESSION['last_key']['oauth_token'] , $_SESSION['last_key']['oauth_token_secret']  );
    $ms  = $c->home_timeline(); // done
    $me = $c->verify_credentials();

    setcookie("sina_member_nickname",$me['name'],time()+3000,"/",".9939.com");
    header("Location:"."http://ask.9939.com/");
    //$rr = $c->update( $_REQUEST['text'] );	//同步信息到微薄
}
?>