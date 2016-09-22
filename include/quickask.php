<?php
session_start();
$_SESSION['token']['token'] = md5(time().'ohgod');
?>
<div class="mod zhmod-04 tMarginLg">
	            <span class="top">
	              <span class="tl"></span>
	            </span>
	            <div class="inner">
	              <div class="hd">
	                <h3>快速提问</h3>
	              </div>
	              <div class="bd">
							<form id="ask_div" name="" method="POST" action="/askt/do/do/save/">
							<input type="hidden" value="<?=$_COOKIE['member_uID']?>" />
						
								<p id="p_showlogin" <?php if($_COOKIE['member_uID']) { echo 'style="display:none"'; }?>>
								  <label>
									我是会员<input type="radio" style="width:10px; border:none;" checked name="register" value="0"  onclick="changeLabel(this, 3, 4);" /></select>
								  </label>
								  <span>（会员登录提问，将获得5积分）</span>   
								  <label>
								  	我要注册<input type="radio" style="width:10px; border:none;" name="register" value="1" onclick="changeLabel(this, 4, 3);" />
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

						<p>
										  科室：
										 	 <span id="load_catid"></span><span id="load_catid_other"></span>
       										 <input type="hidden" name="info[classid]" id="catid" value="" />

       										 
<script type="text/javascript">
var __html_start = '<select onchange="jQuery(\'#catid\').val(this.value);category_load(this.value);"><option value="0">请选择</option>';
var __html_end = '</select>';
function category_load(id)
{

	$.get('/ask/loadcat/', { id: id },
	function(data){
		//$('#load_catid').html(data);
		if(id==0)
		{
			$('#load_catid_other').html('<select><option value="0">请选择</option></select>');
			$('#load_catid').html(__html_start + data + __html_end);
		}
		else
		{

			if(data){
				$('#load_catid_other').html('');
				$('#load_catid_other').append(__html_start + data + __html_end);
			}

		}
	});
}

function checkData() {
	var title = $("#ask_title").val();
	var content = $("#ask_content").val();
	var cid = $("#catid").val();
	if(cid==""||cid==0) {
		alert("请选择科室");
		return false;
	} else if(title=="") {
		alert("请输入提问标题！");
		$("#ask_title").focus();
		return false;
	} else if(content == "") {
		alert("请输入提问内容！");
		$("#ask_content").focus();
		return false;
	} else if(title.length<5 || title.length>50) {
		alert("标题字数要在 5-50 之间");
		$("#ask_title").focus();
		return false;
	} else if(content.length<10 || content.length>500) {
		alert("内容字数要在 10-500 之间");
		$("#ask_content").focus();
		return false;
	} 
}

function __logout(){
	//$("#p_showlogin").show();
	location.reload();
}

function __login(){
	$("#p_showlogin").hide();
}

function category_reload()
{
	$('#load_catid').html('');
	category_load(0);
}
category_load(0);
</script>     										 
									  </p>
								<p>
									<label for="">提问标题：</label><input type="text" name="info[title]" id="ask_title" />
								</p>
								<p>
									<label for="">提问内容：</label><textarea name="info[content]" style="width:510px; height:90px;" id="ask_content"></textarea>
								</p>
								<input type="hidden" value="<?php echo $_SESSION['token']['token']; ?>" name="token"/>
								<p align="center"><input type="submit" class="submit-zh" value="提交" onclick="return checkData();"/></p>
							</form>
	              </div>
	            </div>
	          </div>