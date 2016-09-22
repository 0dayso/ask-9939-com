function getIdgg()
{
	var str = location.href;
	laststr=str.substr(str.length-1,1);

	if(laststr=="/") str=str.substr(0,str.length-1);
	
	var lastindex=str.lastIndexOf("/")+1;
	var clength=str.length-lastindex;
	var ss=str.substr(lastindex,clength);
	
	return parseInt(ss);
}

function getIdggy()
{
	return 1;
}

var adver_id = getIdgg();
var adver_pos_id=getIdggy();

if(!adver_id)
{
	//var adver_id=1;
	adver_id=idgg;
}


function menuControl()
{
	var numID = Math.floor(Math.random()*3) + 1;
	showOneSpan(numID);
}
function showOneSpan(obj)
{
	var s = "menu_" + obj;
	document.getElementById(s).className = "info_on";
}
function hiddenAllSpan()
{
	for(var i=1; i<=3; i++)
	{
		var s = "menu_" + i;
		document.getElementById(s).className = "info_off";
	}
}
function sel(val)
{
	document.getElementById("content").value += val + "\n";
}

function LTrim(s)
{
	for(var i=0;i<s.length;i++)
		if(s.charAt(i)!=' ') return s.substring(i,s.length);
	return "";
}

function RTrim(s)
{
	for(var i=s.length-1;i>=0;i--)
		if(s.charAt(i)!=' ') return s.substring(0,i+1);
	return "";
}

function Trim(s)
{
	return RTrim(LTrim(s));
}


function check(theForm,sp)
{
	var str="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ,./'[]{}`1234567890-=\~!@#$%^&*()_+|?><:";
	if(Trim(theForm.guestname.value) =="")
	{
		alert("请输入姓名");
		theForm.guestname.focus();
		return(false);
	}
	var c=theForm.guestname.value.substr(0,1);
	if(str.indexOf(c)>-1)
	{
		alert("姓名必须填写中文");
		theForm.guestname.focus();
		return(false);
	}
	if(Trim(theForm.phone.value)=="")
	{
		alert("请输入电话");
		theForm.phone.focus();
		return(false);
	}
	if(Trim(theForm.address.value)=="")
	{
		alert("请输入地址");
		theForm.address.focus();
		return(false);
	}
	if(sp == 1)
	{
		if(theForm.zipcode.value == "")
		{
			alert("请输入邮编");
			theForm.zipcode.focus();
			return(false);
		}
	}
	if(theForm.content.value == "")
	{
		alert("请输入留言内容");
		theForm.content.focus();
		return(false);
	}


	document.getElementById("fkfromurl").value=escape(document.location)
	document.getElementById("idgg").value=adver_id;
	document.getElementById("idggy").value=adver_pos_id;
	document.getElementById("ok").disabled = true;
}


document.writeln("<link href=\"http://www.78.cn/style/gbook.css\" rel=\"stylesheet\" type=\"text/css\" />");
document.writeln("<div id=\"gb_09\">");
document.writeln("	<div class=\"gb_title\"><h2>您对该项目是否感兴趣,并希望得到详细资料,请填写留言!</h2></div>");
document.writeln("	<div class=\"gb_connt\">");
document.writeln("		<div class=\"gb_connt_l\" onmouseover=\"hiddenAllSpan()\">");
document.writeln("			<div class=\"b1\">");
document.writeln("				<div class=\"b2\">");
document.writeln("				<h3>温馨提示</h3>");
document.writeln("				<p>将您对项目的需求或疑问填写到留言板内.商家在24小时内联系您，您将在第一时间获取项目详情,助您详细考察项目!</p>");
document.writeln("				<div class=\"b3\"><p>留言是您开启财富之门的第一步，无成本、无风险!请您认真留言,抢占创业先机!</p></div>");
document.writeln("				<p style=\"margin-bottom:0px\">[友情提示] 投资有风险，请详细咨询加盟商，多打电话、多留言、多考察. </p>");
document.writeln("				</div>");
document.writeln("			</div>");
document.writeln("		</div>");
document.writeln("		<div class=\"gb_connt_r\">");
document.writeln("		 <div>");
document.writeln("		   <form onsubmit=\"return check(this)\" action=\"http://3w.adsys.com/gbookadd\" method=\"post\" target=\"_blank\">");
document.writeln("		   <p>姓 名:  <label><input type=\"text\" name=\"guestname\" id=\"guestname\" value=\"\" /></label><em>*</em> 您的真实姓名 </p>");
document.writeln("		   <p>电 话:  <label><input type=\"text\" name=\"phone\" id=\"phone\" value=\"\" /></label><em>*</em> 电话是与您联系的重要方式</p>");
document.writeln("		   <p>手 机:  <label><input type=\"text\" name=\"other\" id=\"other\" value=\"\" /></label><em>&nbsp;</em> 请正确填写,便于和你联系 </p>");
document.writeln("		   <p>邮 箱:  <label><input type=\"text\" name=\"email\" id=\"email\" value=\"\" /></label><em>&nbsp;</em> 请正确填写电子信箱</p>");
document.writeln("		   <p>地 址:  <label><input type=\"text\" name=\"address\" id=\"address\" value=\"\" /></label><em>*</em> 与您联系的重要方式</p>");
document.writeln("		   ");
document.writeln("		   <p>邮 编:  <label><input type=\"text\" name=\"zipcode\" id=\"zipcode\" value=\"\" onmouseover=\"hiddenAllSpan()\" /></label><em>*</em> 以保证资料邮递&nbsp; </p>");
document.writeln("		   <div class=\"gb_txt\"><textarea name=\"content\" cols=\"50\" rows=\"10\" id=\"content\" onmouseover=\"menuControl()\"></textarea>");
document.writeln("			<div style=\"float:left\"><div id=\"gb_kj\">");
document.writeln("			<div id=\"menu_1\" class=\"info_off\">");
document.writeln("			<p>请填写留言或根据意向选择下列快捷留言</p>");
document.writeln("			<ul>");
document.writeln("			<li><a href=\"javascript:sel(\'请问我这个地方有加盟商了吗？\')\">请问我这个地方有加盟商了吗？ </a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'我想加盟，请来电话告诉我具体细节。\')\">我想加盟，请来电话告诉我具体细节。 </a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'想了解加盟细节，请尽快寄一份资料。\')\">想了解加盟细节，请尽快寄一份资料。</a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'给您们发过留言，未收到资料，请给我来电。\')\">给您留过言，未收到资料，请给我来电。</a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'很感兴趣，想知道加盟细节。\')\">很感兴趣，想知道加盟细节。</a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'我对这个项目很感兴趣,请尽快寄资料。\')\">我对这个项目很感兴趣,请尽快寄资料。</a></li>");
document.writeln("			</ul></div>");
document.writeln("			<div id=\"menu_2\" class=\"info_off\">");
document.writeln("			<p>请填写留言或根据意向选择下列快捷留言</p>");
document.writeln("			<ul>");
document.writeln("			<li><a href=\"javascript:sel(\'项目很好，现在就想加盟，请给我预留名额。\')\">给您留过言，未收到资料，请给我来电。</a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'请问贵公司哪里有样板店或直营店？\')\">请问贵公司哪里有样板店或直营店？</a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'请给我打电话，并寄加盟资料。\')\">请给我打电话，并寄加盟资料。</a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'很想合作，来电话细谈吧。\')\">很想合作，来电话细谈吧。</a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'对这个项目很感兴趣，请尽快寄资料。\')\">对这个项目很感兴趣，请尽快寄资料。</a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'我很想代理您的项目，请来电或寄资料。\')\">我很想代理您的项目，请来电或寄资料。</a></li>");
document.writeln("			</ul></div>");
document.writeln("			<div id=\"menu_3\" class=\"info_off\">");
document.writeln("			<p>请填写留言或根据意向选择下列快捷留言</p>");
document.writeln("			<ul>");
document.writeln("			<li><a href=\"javascript:sel(\'我加盟后，您们还会提供哪些服务？\')\">我加盟后，您们还会提供哪些服务？</a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'有兴趣开一个店，请寄资料或给我打电话。\')\">有兴趣开一个店，请寄资料或给我打电话。</a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'请问我这个地方有加盟商了吗？\')\">请问我这个地方有加盟商了吗？</a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'请将详细投资方案和资料寄给本人。\')\">请将详细投资方案和资料寄给本人。</a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'初步打算加盟贵公司，请寄资料。\')\">初步打算加盟贵公司，请寄资料。</a></li>");
document.writeln("			<li><a href=\"javascript:sel(\'电话未联系上，请给我来电或发邮件。\')\">电话未联系上，请给我来电或发邮件。</a></li>");
document.writeln("			</ul></div>");
document.writeln("			</div></div>");
document.writeln("		   </div>");
document.writeln("		   <p style=\"padding:0 0 0 64px\" onmouseover=\"hiddenAllSpan()\"><button type=\"submit\" class=\"an1\" id=\"ok\" value=\" \">提交按钮</button>&nbsp; <button name=\"\" type=\"reset\" class=\"an2\" value=\" \">重写</button></p>");
document.writeln("			<input type=\"hidden\" name=\"idgg\" id=\"idgg\">");
document.writeln("			<input id=\"fkfromurl\" type=\"hidden\" name=\"fkfromurl\">");
document.writeln("		   </form>");
document.writeln("		 </div>");
document.writeln("		</div>");
document.writeln("	</div>");
document.writeln("</div>");
