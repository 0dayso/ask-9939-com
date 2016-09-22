;(function () {
	var zInedxNum=999;
	var isStrict = document.compatMode == "CSS1Compat",m = Math.max,d = document, dd = d.documentElement, db = d.body;
	var getPageWH = function(){
                return {
                    h: isStrict ? m(dd.clientHeight, dd.scrollHeight) : m(db.clientHeight, db.scrollHeight),
                    w: isStrict ? m(dd.clientWidth, dd.scrollWidth) : m(db.clientWidth, db.scrollWidth)
                }
     }
    //遮着层类
	var maskLayer=function() {
		if (this.init) this.init.apply(this,[].slice.call(arguments,0));
	};
	maskLayer.prototype.init=function(color,opacity) {
		var wh=getPageWH();
		this.color=color;
		this.opacity=opacity;
		this.iframeElment=$("<iframe frameBorder='0'></iframe>");
		this.iframeElment.css({'width':wh.w+'px','height':wh.h+'px','display':'none','opacity': '0','position':'absolute','top':'0','left':'0'});
		this.layerElement=$("<div></div>");
		this.layerElement.css({'width':wh.w+'px','height':wh.h+'px','display':'none','opacity': '0','position':'absolute','top':'0','left':'0'});
		this.iframeElment.appendTo(document.body);
		this.layerElement.appendTo(document.body);
		var that=this;
		$(window).resize(function(){
			that.iframeElment.css({'width':'auto','height':'auto'});
			that.layerElement.css({'width':'auto','height':'auto'});
			var wh=getPageWH();
			console.log(wh.w+':'+wh.h);
			that.iframeElment.css({'width':wh.w+'px','height':wh.h+'px'});
			that.layerElement.css({'width':wh.w+'px','height':wh.h+'px'});
			});
	};
	maskLayer.prototype.show=function() {
		this.iframeElment.css({'display':'block','opacity':this.opacity,'z-index':zInedxNum++,'background':this.color});
		this.layerElement.css({'display':'block','opacity':this.opacity,'z-index':zInedxNum++,'background':this.color});
	};
	maskLayer.prototype.hide=function() {
		this.iframeElment.css('display','none');
		this.layerElement.css('display','none');
	};
	maskLayer.prototype.state=function() {
		if (this.layerElement.css('display')==='none') return 'hidden';
		return 'visible';
	};
	//遮着享原控制器
	this.maskLayerManger=(function() {
		var created=[];
		return{
			displaymaskLayer:function(color,opacity) {
				var layer=null,j=created.length,inuser=this.inUseNum();
				if (inuser>=j) {
					created.push(this.createmaskLayer(color,opacity))
					layer=created[created.length-1];
					}else{
						layer=created[inuser];
						layer.color=color;
						layer.opacity=opacity
					}
				return layer;
			},
			createmaskLayer:function(color,opacity) {
				return new maskLayer(color,opacity);
			},
			inUseNum:function() {
					var inuser=0;
					for (var i=0,j=created.length; i<j ; i++) {
						if (created[i].state()=="visible") inuser++;
						else return inuser;
					}
					return inuser;
				}
			}
		})();
	var boxDialog=function() {
		if (this.init) this.init.apply(this,[].slice.call(arguments,0));
	};
	boxDialog.prototype.init=function(html) {
		this.element=$('<div class="mod tmod-02"><span class="top"><span class="tl"></span><span class="tr"></span></span><div class="inner"></div><span class="bottom"></span></div>');
		this.html=html;
		this.element.css("display","none");
		this.element.appendTo(document.body);
	};
	boxDialog.prototype.show=function() {
		this.element.find('.inner').html(this.html);
		if ($.browser.msie&&((+$.browser.version)<7)) {
this.element.css({'position':'absolute','left':'50%','top':'50%','margin-left':($(document.documentElement).scrollLeft()-(this.element.width()/2))+'px','margin-top':($(document.documentElement).scrollTop()-(this.element.height()/2))+'px','z-index':zInedxNum++});
var that=this;
	$(window).scroll(function() {
	that.element.css({'margin-left':($(document.documentElement).scrollLeft()-(that.element.width()/2))+'px','margin-top':($(document.documentElement).scrollTop()-(that.element.height()/2))+'px'});
	$('#test').html($(document.documentElement).scrollTop())
	})
		}else{
		this.element.css({'position':'fixed','left':'50%','top':'50%','margin-left':'-'+(this.element.width()/2)+'px','margin-top':'-'+(this.element.height()/2)+'px','z-index':zInedxNum++});
		}
		this.element.css("display","block");
	};
	boxDialog.prototype.hide=function() {
		this.element.css("display","none");
	};
	boxDialog.prototype.state=function() {
		if (this.element.css('display'=='none')) return 'hidden';
		return 'visible'
	};
	this.boxDialogManger=(function() {
		var created=[];
		return{
			displayboxDialog:function(html) {
				var box=null,j=created.length,inuser=this.inUseNum();
				if (inuser>=j) {
					created.push(this.createboxDialog(html))
					box=created[created.length-1];
					}else{
						box=created[inuser];
						box.html=html;
					}
				return box;
			},
			createboxDialog:function(html) {
				return new boxDialog(html);
			},
			inUseNum:function() {
					var inuser=0;
					for (var i=0,j=created.length; i<j ; i++) {
						if (created[i].state()=="visible") inuser++;
						else return inuser;
					}
					return inuser;
				}
			}
	})()
	var compoDialog=function() {
		if (this.init) this.init.apply(this,[].slice.call(arguments,0));
	};
	compoDialog.prototype.init=function() {
		this.compo=[];
	};
	compoDialog.prototype.add=function(o) {
		this.compo.push(o);
	};
	compoDialog.prototype.show=function() {
		for (var i=0,j=this.compo.length; i < j; i++) {
			this.compo[i].show();
		};
	};
	compoDialog.prototype.hide=function() {
		for (var i=0,j=this.compo.length; i < j; i++) {
			this.compo[i].hide();
		};
	};
	this.compoDialog=compoDialog;
})();