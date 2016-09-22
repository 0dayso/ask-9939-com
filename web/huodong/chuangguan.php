<?php
@extract($_REQUEST);
require 'config.php';
require 'func_huodong.php';
$db = DBconnect(1);

if(!$_COOKIE['member_uID']||!$_COOKIE['passed']){
	echo '<script>alert("请先提问！");location.href="index.php";</script>';
}

$aQuesAll = @include('question.php');
$count = count($aQuesAll);
$Qnum = getQuestion();
foreach($Qnum as $k=>$v){
	preg_match('~<([0-9]+)>~is',$v,$a);
	preg_match('~\(\s*([^\)]*)\)~is',$v,$b);
	$aYes[$a[1]]=$b[1];
	$v = preg_replace('~<[0-9]+>~is',$k,$v);//增加排序号
	$v = preg_replace('~\(\s*[^\)]*\)~is','()',$v);//去掉答案
	$v = preg_replace('~A~s',"<br/>A",$v);
	$v = preg_replace('~([A|B|C|D]+)~s',"<input name=\"info[".$a[1]."]\" value=\"\${1}\" type=\"radio\" onclick=\"checkrad('$a[1]')\"/>\${1}",$v);//增加单选
	$str .= $v.'<br/>';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>闯关</title>
<link href="style/r.css" type="text/css" rel="stylesheet" />
<link href="style/b.css" type="text/css" rel="stylesheet" />
<link href="style/g.css" type="text/css" rel="stylesheet" />
<link href="style/m.css" type="text/css" rel="stylesheet" />
<link href="style/activity.css" type="text/css" rel="stylesheet" />
<link href="style/activitySub.css" type="text/css" rel="stylesheet" />
<script src="js/jquery.js"></script>
<script>
function in_array(needle, haystack) {
	type = typeof needle
	 if(type == "string" || type =="number") {
	  for(var i in haystack) {
	   if(haystack[i] == needle) {
	     return true;
	   }
	  }
	 }
	 return false;
}

function checkrad(key){
	var s = jQuery('#hidelist').val();
	var arr=new Array();
	arr = s.split(",");
	if(key=='sub'){
		if(arr.length<10)
			alert('请完善您的答案。');
		else
			jQuery('#ql').submit();
	}else{
		if(!in_array(key,arr)){
			if(s=='')
			jQuery('#hidelist').val(key);
			else
			jQuery('#hidelist').val(s+','+key);
		}
	}
}

</script>
<?php
//if(!$_COOKIE['passed'])
//echo '<script>alert("请先登录！");location.href="index.php"</script>';
?>
</head>

<body>
  <div id="doc" class="w950">
    <div id="hd">
    <?php include 'header.html';?>
    </div>
    <div id="bd">
      <div class="mod emod-02">
        <span class="top">
          <span class="tl">
          </span>
          <span class="tr">
          </span>
        </span>
        <div class="inner">
         <?php @include('nav.html');?>
          <div class="lw-subMod l-fix">
          	<div class="t subBg"><h1>闯关</h1><span>规则：下面十个问题，只有你答对7个或7个以上才有能参加抽奖。</span></div>
          	<div class="c">
          		<div class="lw-qgmod">
          			<form name="qlist" id="ql" action="cgCheck.php" method="POST">
          			<?php echo $str;?>
					<span>
						<input type="hidden" id="hidelist"/>
						<input type="button" id="listsub" class="btn" onclick="return checkrad('sub');"/>
					</span>
					</form>
          		</div>
          	</div>
          </div>
        </div>
        <span class="bottom">
          <span class="bl">
          </span>
          <span class="br">
          </span>
        </span>
      </div>
    </div>
    <div id="ft">
    ft
    </div>
  </div>
</body>
</html>
