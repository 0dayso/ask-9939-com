/*
 *	edit by Styling
 *	2012-6-5
 */
var dom_ = {
	addEvent : function(elem, ev, fn) {
		if(elem.addEventListener)
			elem.addEventListener(ev, fn, false);
		else
			elem.attachEvent("on" + ev, fn, false);
	},
	removeEvent : function(elem, ev, fn) {
		if(elem.removeEventListener)
			elem.removeEventListener(ev, fn, false);
		else
			elem.detachEvent(ev, fn, false);
	},
	byId : function(id) {
		return typeof id=="string" ? document.getElementById(id) : id
	},
	byClass : function(nBox, nClass) {
		var nArr = [];
		var nChild = dom_.byId(nBox).getElementsByTagName("*"),
			len = nChild.length,
			i = 0,
			rClass = new RegExp("(^| )" + nClass + "( |$)");
		for(;i<len;i++) {
			rClass.test(nChild[i].className) && nArr.push(nChild[i]);
		}
		return nArr;
	},
	isIE : navigator.appVersion.indexOf("MSIE") != -1 ? true : false

};
/*-------------------------- +
  get
 +-------------------------- */
var get = {
	byId: function(id) {
		return typeof id === "string" ? document.getElementById(id) : id
	},
	byClass: function(tagName, sClass, oParent) {
		var aClass = [];
		var reClass = new RegExp("(^| )" + sClass + "( |$)");
		var aElem = this.byId(oParent).getElementsByTagName(tagName)
		for (var i = 0; i < aElem.length; i++) reClass.test(aElem[i].className) && aClass.push(aElem[i]);
		return aClass
	},
   	nextNode : function(fNode){
		var nNode = fNode.nextSibling;
		while(nNode.nodeType != 1){
			nNode = nNode.nextSibling;
		}
		return nNode;
	},
	lastNode : function(fNode){
		var lNode = fNode.previousSibling;
		while(lNode.nodeType != 1){
			lNode = lNode.previousSibling;
		}
		return lNode;
	}
};

/*scrollbar*/
function scrollbar (obj) {
	var sbox = dom_.byId(obj);
	var iHeight = sbox.offsetTop;
	var iWidth = document.documentElement.clientWidth;
	var timer = null;
	dom_.addEvent(window, "scroll", function () {
		var iTop = document.documentElement.scrollTop==0 ? document.body.scrollTop : document.documentElement.scrollTop;	
		sbox.style.width = iWidth + "px";
		sbox.style.left = 0;
		if(iTop > iHeight){
			if(dom_.isIE && !window.XMLHttpRequest){
				document.body.position = "relative";
				sbox.style.position = "absolute";		
				sbox.style.top = iTop + "px";		
			}else{
				sbox.style.position = "fixed";
				sbox.style.top = 0;
			}
			sbox.className = 'scrollbar';
		}else{
			sbox.style.position = "relative";
			sbox.style.top = 0;
			sbox.className = 'scrollbar';
		}
	});
}
if(dom_.byId("scrollbar")){
	scrollbar("scrollbar");	
}