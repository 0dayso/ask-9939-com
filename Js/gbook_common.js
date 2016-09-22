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
	//var adver_id={adver_id};
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

