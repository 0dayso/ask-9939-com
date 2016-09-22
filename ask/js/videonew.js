var me = new flvMedia();
function $(element){
	if(typeof element == "string"){
		return document.getElementById(element);
	}
	return element;
}
function flvMedia(){
	var flvObj = this;
	var iiii = 0;
	this.flvName = createFlvName();
	this.autoPlay = function(id){
//		if(window.ActiveXObject){
//			var sel = eiframe.iwin.document.selection,
//				rng = sel.createRange();
//			if(eiframe.bookMark != null){
//				rng.moveToBookmark(eiframe.bookMark);
//			} else {
//				eiframe.bookMark = rng.getBookmark();
//			}
//		}
		iiii += 1;
		var flashBot = $(id),
			tagDisp = flashBot.style.display;
		var aryBot = ["mediaSound","mediaVideo","upload"];
		for(var i in aryBot){
			if(id == aryBot[i]){
				if(!flashBot.title){
					flashBot.style.display = (tagDisp == "none") ? "block" : "none";
					if(id == aryBot[0] || id == aryBot[1]){
						reConNet();
						resetDoneValue();
					}
				}
			} else {
				$(aryBot[i]).style.display = "none";
			}
		}
		resizeIframe();
	}
	function createFlvName(){
		var d = new Date();
		var randomNum = parseInt(Math.random()*10000);
		return d.getTime().toString()+randomNum.toString();
	}
	function reConNet(){
		if($("mediaSound")){
			$("mediaSound").innerHTML = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0\" width=\"378\" height=\"140\"><param name=\"movie\" value=\"../../newFlash/newRecordSound.swf?flvsite=1&flvName="+me.flvName+"&i="+iiii+"\" /><param name=\"quality\" value=\"high\" /><embed src=\"../../newFlash/newRecordSound.swf?flvsite=1&flvName="+me.flvName+"&i="+iiii+"\" quality=\"high\" pluginspage=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"378\" height=\"140\"></embed></object>";
		}
		if($("mediaVideo")){
			$("mediaVideo").innerHTML = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0\" width=\"334\" height=\"288\"><param name=\"movie\" value=\"../../newFlash/newRecordVideo.swf?flvsite=1&flvName="+me.flvName+"&i="+iiii+"\" /><param name=\"quality\" value=\"high\" /><embed src=\"../../newFlash/newRecordVideo.swf?flvsite=1&flvName="+me.flvName+"&i="+iiii+"\" quality=\"high\" pluginspage=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"334\" height=\"288\"></embed></object>";
		}
	}
	function resetDoneValue(){
		$("done").value = 1;
		$("z_span2").onmousedown = function(){flvObj.autoPlay("mediaVideo");};
		$("z_span3").onmousedown = function(){flvObj.autoPlay("mediaSound");};
		//$("tsWords").innerHTML = "";
	}
}
$("mediaSound").innerHTML = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0\" width=\"378\" height=\"140\"><param name=\"movie\" value=\"../../newFlash/newRecordSound.swf?flvsite=1&flvName="+me.flvName+"\" /><param name=\"quality\" value=\"high\" /><embed src=\"../../newFlash/newRecordSound.swf?flvsite=1&flvName="+me.flvName+"\" quality=\"high\" pluginspage=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"378\" height=\"140\"></embed></object>"
$("mediaVideo").innerHTML = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0\" width=\"334\" height=\"288\"><param name=\"movie\" value=\"../../newFlash/newRecordVideo.swf?flvsite=1&flvName="+me.flvName+"\" /><param name=\"quality\" value=\"high\" /><embed src=\"../../newFlash/newRecordVideo.swf?flvsite=1&flvName="+me.flvName+"\" quality=\"high\" pluginspage=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"334\" height=\"288\"></embed></object>"
function externalSound(flvsite){
	if(parseInt(flvsite)==1){
		//var v = $("ctrlVideo");
		//v.onmousedown   = function(){};
		//v.title     = "您已经添加音频,不能再添加视频了";
		$("tsWords").innerHTML = "添加音频成功"; 
		$("mediaSound").style.display = "none";
		$("done").value = 3;
		Q("#z_ul1").fadeIn(100);
		z_none(btn1,btn2a,btn3a);z_block(btn1a,btn2,btn3);
		iscontnet = true;
		resizeIframe();
	}
}
function externalVideo(flvsite){
	if(parseInt(flvsite)==1){
		//var r = $("ctrlRadio");
		//r.onmousedown = function(){};
		//r.title   = "您已经添加视频,不能再添加音频了";
		$("tsWords").innerHTML = "添加视频成功"; 
		$("mediaVideo").style.display = "none";
		$("done").value = 2;		
		Q("#z_ul1").fadeIn(100);
		z_none(btn1,btn2a,btn3a);z_block(btn1a,btn2,btn3);
		iscontnet = true;
		resizeIframe();
	}
}

function resizeIframe()
{
	return;
	if(document.body.scrollHeight == "0"&&parent.document.getElementById("formpage"))
		parent.document.getElementById("formpage").height = document.documentElement.scrollHeight + "px";
}