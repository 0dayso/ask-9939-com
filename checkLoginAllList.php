<?php 
	if(!$_COOKIE['member_uID']) { 
		$str = '
		<p>
		  <label>
			我是会员<input type="radio" style="width:10px;" checked name="register" value="0"  onclick="changeLabel(this, 3, 4);" /></select>
		  </label>
		  <span>（会员登录提问，将获得5积分）</span>   
		  <label>
		  	我要注册<input type="radio" style="width:10px;" name="register" value="1" onclick="changeLabel(this, 4, 3);" />
		  </label>
		  <br />
		  <span id="label_3">
			邮箱：<input type="text" name="username" style="width:200px;" />

			  
			密码：<input type="password" name="pwd" style="width:200px;" />
		  </span>
		  <span id="label_4" style="display:none;">
			邮箱：<input type="text" name="mail" style="width:260px;" />
		  </span>
		</p>
				';
		echo $str;
		exit;
		$str = preg_replace("/\s*/", ' ', $str);
		ECHO "document.write('<?=$str?>');";
	}
?>