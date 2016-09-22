// JavaScript Document
var classId = 0;
var className = '';
var input_obj;
var hidden_name;
var hidden_id;
var box_obj;

jQuery.extend({
	__select_class:function(obj,showObj){
		Q(function(){
			//value
			box_obj = obj;
			var str = '';
			var lv1id,lv2id,lv3id,lv4id;
			showObj.css("z-index","100");
			//showObj.css('background','url(/js/jquery.selector_class/select.gif) left 0 no-repeat');
			//showObj.css('width','180px');
			//showObj.css('height','21px');
			//showObj.css('line-height','21px');
			//showObj.css('padding-left','10px');
			//showObj.css('cursor','pointer');
			//html			
			var ___div = '';
			___div += '<div class="__box" style="border:1px solid #80a44b; background:#ffffff; display:none; margin:0px; padding:0px; width:595px; position:absolute; z-index:1000; left: 265px; top: 527px;">';
			___div += '<div class="classTitle" style="background:url(../images_ask/images/ewq1.jpg) 0 top repeat-x;height:39px;overflow:hidden;"><div style="float:left; margin-left:10px; height:39px; line-height:39px; font-size:14px; font-weight:bolder; color:#1f376d;">您选择的科室为：</div><font class="selectedLv1" style="float:left;color:#1f376d; height:39px; line-height:39px; font-weight:bolder;"></font><font class="selectedLv2" style="float:left;color:#1f376d; height:39px; line-height:39px; font-weight:bolder;"></font><font class="selectedLv3" style="float:left;color:#1f376d;; height:39px; line-height:39px; font-weight:bolder;"></font><font class="selectedLv4" style="float:left;color:##1f376d; height:39px; line-height:39px; font-weight:bolder;"></font><div class="closeBox" style="float:right;width:16px;height:16px;cursor:pointer;background:url(../images_ask/images/qweqw.jpg) 0 9px no-repeat;padding:9px 12px 0  0;"></div></div>';
			___div += '<div class="level1" style=" margin:4px;">';
			___div += '<div class="lvTitle" style=" height:20px; line-height:20px; color:#1f376d; padding-left:6px; font-size:14px; font-weight:bolder; text-align:left;">一级科室</div>';
			___div += '<div class="lv1Box">加载中...</div>';
			___div += '<div style="clear:both;margin:0px;padding:0px;font-size:0px;line-height:0px;height:0px;"></div>';
			___div += '</div>';
			___div += '<div class="level2" style=" margin:4px;">';
			___div += '<div class="lvTitle" style=" height:20px; line-height:20px; color:#1f376d; padding-left:6px; font-size:14px; font-weight:bolder;text-align:left;">二级科室</div>';
			___div += '<div class="lv2Box"></div>';
			___div += '<div style="clear:both;margin:0px;padding:0px;font-size:0px;line-height:0px;height:0px;"></div>';
			___div += '</div>';
			___div += '<div class="level3" style=" margin:4px;">';
			___div += '<div class="lvTitle" style=" height:20px; line-height:20px; color:#1f376d; padding-left:6px; font-size:14px; font-weight:bolder;text-align:left;">三级科室</div>';
			___div += '<div class="lv3Box"></div>';
			___div += '<div style="clear:both;margin:0px;padding:0px;font-size:0px; line-height:0px;height:0px;"></div>';
			___div += '</div>';
			___div += '<div class="level4" style=" margin:4px;">';
			___div += '<div class="lvTitle" style=" height:20px; line-height:20px; color:#1f376d; padding-left:6px; font-size:14px; font-weight:bolder;text-align:left;">四级科室</div>';
			___div += '<div class="lv4Box"></div>';
			___div += '<div style="clear:both;margin:0px;padding:0px;font-size:0px;line-height:0px;height:0px;"></div>';
			___div += '</div>';
			//常见科室start
			___div += '<div class="common" style=" margin:4px; padding:8px; border:1px solid #1f376d;">';
			___div += '<div style=" height:20px; line-height:20px; color:#1f376d; padding-left:6px; font-size:14px; font-weight:bolder;text-align:left;">常见科室:</div>';
			___div += '<ul id="common_class">';
			___div += '<li title="1,12,16">风湿</li>';
			___div += '<li title="3,45">不孕不育</li>';
			___div += '<li title="3,244,399">避孕</li>';
			___div += '<li title="3,244,400">人流</li>';
			___div += '<li title="3,244,401">引产</li>';
			___div += '<li title="2,27,141,260">颈椎病</li>';
			___div += '<li title="2,27,142,261">腰椎病</li>';
			___div += '<li title="2,26,37">肛肠疾病</li>';
			___div += '<li title="1,15,18">糖尿病</li>';
			___div += '<li title="69,236">血管瘤</li>';
			___div += '<li title="2,27,251">强直性脊柱炎</li>';
			___div += '<li title="7,58,164">牛皮癣</li>';
			___div += '<li title="2,30">心胸外科</li>';
			___div += '<li title="63,64">乙肝</li>';
			___div += '<li title="2,27,235">股骨头坏死</li>';
			___div += '<li title="2,26,139">周围血管病</li>';
			___div += '<li title="7,58,163">白癜风</li>';
			___div += '<li title="68,389">植发</li>';
			___div += '<li title="1,5,262">哮喘</li>';
			___div += '<li title="1,12,313">红斑狼疮</li>';
			___div += '<li title="1,15,19">甲状腺疾病</li>';
			___div += '</ul>';
			___div += '<div style="clear:both;margin:0px;padding:0px;font-size:0px;line-height:0px;height:0px;"></div>';
			___div += '</div>';
			//常见科室end
			___div += '<div class="selectedSubmit" style="text-align:center; height:40px; line-height:40px;background:#ffffff;"><input id="selectedEnter" name="selectedEnter" style="background:url(../images_ask/images/queding.jpg) center 0 no-repeat; width:70px; height:25px; border:1px;cursor:pointer;" type="button" value=" " style="margin-top:4px;" /></div>';
			___div += '</div>';
			Q('body').append(___div);
			//ajax
			Q.getJSON("http://per.120ask.com/index.php?c=api_for_ask&m=api_selector_getClassList&jsoncallback=?", function(json){
				str = '<ul style=" margin:0px; padding:0px;">';
				for(i=0;i<json.level1.length;i++){
					str += '<li class="lv1" style="list-style-type:none; border:0px; cursor:pointer; padding:0px; height:20px; line-height:20px; float:left; margin:4px; text-decoration:underline; color:#02418F;" title="'+json.level1[i].id+'">'+json.level1[i].name+'</li>';
				}
				str += '</ul>';
				Q('.__box .lv1Box').html(str);
				Q('.__box .level2').css('display','none');
				Q('.__box .level3').css('display','none');
				Q('.__box .level4').css('display','none');
		
				Q('.__box .lv1').each(function(){
					Q(this).click(function(){
						lv1id = Q(this).attr('title');
						classId = lv1id;
						className = Q(this).html();
						for(i=0;i<json.level1.length;i++){
							if(json.level1[i].id==lv1id){
								if(json.level1[i].level2){
									var lvi = i;
									str = '<ul>';
									for(ii=0;ii<json.level1[i].level2.length;ii++){
										str += '<li class="lv2" style="list-style-type:none; border:0px; cursor:pointer; padding:0px; height:20px; line-height:20px; float:left; margin:4px; text-decoration:underline; color:#02418F;" title="'+json.level1[i].level2[ii].id+'">'+json.level1[i].level2[ii].name+'</li>';
									}
									str += '</ul>';
									Q('.__box .lv2Box').html(str);
									Q('.__box .lv3Box').html('');
		
									Q('.__box .lv2').each(function(){
										Q(this).click(function(){
											lv2id = Q(this).attr('title');
											classId = lv2id;
											className = Q(this).html();
											for(ii=0;ii<json.level1[lvi].level2.length;ii++){
												if(json.level1[lvi].level2[ii].id==lv2id){
													if(json.level1[lvi].level2[ii].level3){
														var lvii = ii;
														str = '<ul>';
														if(json.level1[lvi].level2[ii].level3.name){
															str += '<li class="lv3" style="list-style-type:none; border:0px; cursor:pointer; padding:0px; height:20px; line-height:20px; float:left; margin:4px; text-decoration:underline; color:#02418F;" title="'+json.level1[lvi].level2[ii].level3.id+'">'+json.level1[lvi].level2[ii].level3.name+'</li>';
														}
														for(iii=0;iii<json.level1[lvi].level2[ii].level3.length;iii++){
															str += '<li class="lv3" style="list-style-type:none; border:0px; cursor:pointer; padding:0px; height:20px; line-height:20px; float:left; margin:4px; text-decoration:underline; color:#02418F;" title="'+json.level1[lvi].level2[ii].level3[iii].id+'">'+json.level1[lvi].level2[ii].level3[iii].name+'</li>';
														}
														str += '</ul>';
														Q('.__box .lv3Box').html(str);
		
														Q('.__box .lv3').each(function(){
															Q(this).click(function(){
																lv3id = Q(this).attr('title');
																classId = lv3id;
																className = Q(this).html();
																for(iii=0;iii<json.level1[lvi].level2[lvii].level3.length;iii++){
																	if(json.level1[lvi].level2[lvii].level3[iii].id==lv3id){
																		if(json.level1[lvi].level2[lvii].level3[iii].level4){
																			var lviii = iii;
																			str = '<ul>';
																			for(iiii=0;iiii<json.level1[lvi].level2[lvii].level3[iii].level4.length;iiii++){
																				str += '<li class="lv4" style="list-style-type:none; border:0px; cursor:pointer; padding:0px; height:20px; line-height:20px; float:left; margin:4px; text-decoration:underline; color:#02418F;" title="'+json.level1[lvi].level2[lvii].level3[iii].level4[iiii].id+'">'+json.level1[lvi].level2[lvii].level3[iii].level4[iiii].name+'</li>';
																			}
																			if(json.level1[lvi].level2[lvii].level3[iii].level4.name){
																				str += '<li class="lv4" style="list-style-type:none; border:0px; cursor:pointer; padding:0px; height:20px; line-height:20px; float:left; margin:4px; text-decoration:underline; color:#02418F;" title="'+json.level1[lvi].level2[lvii].level3[iii].level4.id+'">'+json.level1[lvi].level2[lvii].level3[iii].level4.name+'</li>';
																			}
																			str += '</ul>';
																			Q('.__box .lv4Box').html(str);
		
																			Q('.__box .lv4').each(function(){
																				Q(this).click(function(){
																					lv4id = Q(this).attr('title');
																					classId = lv4id;
																					className = Q(this).html();
																					Q('.__box .selectedLv4').html('>>'+Q(this).html());
																					Q(this).parent().children().css('background','#fff');
																					Q(this).css('background','#FF6000');
																					Q(this).parent().children().css('color','#02418F');
																					Q(this).css('color','#FFFFFF');
																					Q('#common_class li').css('background','#fff');
																					Q('#common_class li').css('color','#02418F');
																				});
																			});
																			Q('.__box .level4').css('display','block');
																		}else{
																			Q('.__box .level4').css('display','none');
																		}
																	}
																}
																Q('.__box .selectedLv3').html('>>'+Q(this).html());
																Q('.__box .selectedLv4').html('');
																Q(this).parent().children().css('background','#fff');
																Q(this).css('background','#FF6000');
																Q(this).parent().children().css('color','#02418F');
																Q(this).css('color','#FFFFFF');
																Q('#common_class li').css('background','#fff');
																Q('#common_class li').css('color','#02418F');
															});
														});
														Q('.__box .level3').css('display','block');
													}else{
														Q('.__box .level3').css('display','none');
													}
												}
											}
											Q('.__box .selectedLv2').html('>>'+Q(this).html());
											Q('.__box .selectedLv3').html('');
											Q('.__box .selectedLv4').html('');
											Q('.__box .level4').css('display','none');
											Q(this).parent().children().css('background','#fff');
											Q(this).css('background','#FF6000');
											Q(this).parent().children().css('color','#02418F');
											Q(this).css('color','#FFFFFF');
											Q('#common_class li').css('background','#fff');
											Q('#common_class li').css('color','#02418F');
										});
									});
									Q('.__box .level2').css('display','block');
								}else{
									Q('.__box .level2').css('display','none');
								}
							}
						}
						Q('.__box .selectedLv1').html(Q(this).html());
						Q('.__box .selectedLv2').html('');
						Q('.__box .selectedLv3').html('');
						Q('.__box .selectedLv4').html('');
						Q('.__box .level3').css('display','none');
						Q('.__box .level4').css('display','none');
						Q(this).parent().children().css('background','#fff');
						Q(this).css('background','#FF6000');
						Q(this).parent().children().css('color','#02418F');
						Q(this).css('color','#FFFFFF');
						Q('#common_class li').css('background','#fff');
						Q('#common_class li').css('color','#02418F');
					});
				});
			});
			box_obj.append(Q('.__box'));
			Q('.__box').hide();
			//common_class
			Q('#common_class li').css({
				'list-style-type':'none',
				'border':'0px',
				'cursor':'pointer',
				'padding':'0px',
				'height':'20px',
				'line-height':'20px',
				'float':'left',
				'margin':'4px',
				'text-decoration':'underline',
				'color':'#02418F'		  
			});
			Q('#common_class li').each(function(){
				Q(this).click(function(){
					var id = Q(this).attr('title').split(",");
					for(j=0;j<id.length;j++){
						for(i=0;i<Q('.lv'+(j+1)).length;i++){
							if(Q('.lv'+(j+1)).eq(i).attr('title')==id[j]){
								Q('.lv'+(j+1)).eq(i).click();
							}
						}
					}
					Q('#common_class li').css('background','#fff');
					Q('#common_class li').css('color','#02418F');
					Q(this).css('background','#FF6000');
					Q(this).css('color','#FFFFFF');
				});						
			});
			//event
			Q(".closeBox").click(function(){
				Q('.__box').hide();
			});
			Q('#selectedEnter').click(function(){
				if(classId==0){
					alert('请选择科室！');
				}else{
					Q.__select_class_input(className,classId);
					Q(".__box").hide();
				}
			});
		});
	},
	//event
	__select_class_evt:function(input,h_name,h_id){
		Q('.__box').show();
		//Q('.__box').css({'left':Q(window).width()/2-parseInt(Q('.__box').width())/2+'px','top':Q(window).height()/2-parseInt(Q('.__box').height())/2+'px'});
		input_obj = input;
		hidden_name = h_name;
		hidden_id = h_id;
	},
	__select_class_id:function(){
		return classId;
	},
	__select_class_name:function(){
		return className;
	},
	__select_class_input:function(n,i){
		if(input_obj){
			input_obj.html(n+'（点击可修改）');
		}
		if(hidden_name){
			hidden_name.val(n);
		}
		if(hidden_id){
			hidden_id.val(i);
		}
	},
	__select_class_set:function(ids,input_obj,hidden_name,hidden_id){
		//Q('.__box').show();
		var id = ids.split("|");
		var s = setInterval(function(){
			if(Q('.lv1').length>0){
				clearInterval(s);
				for(j=0;j<id.length;j++){
					for(i=0;i<Q('.lv'+(j+1)).length;i++){
						if(Q('.lv'+(j+1)).eq(i).attr('title')==id[j]){
							Q('.lv'+(j+1)).eq(i).click();
						}
					}
				}
				input_obj.html(Q.__select_class_name()+'（点击可修改）');
				hidden_name.val(Q.__select_class_name());
				hidden_id.val(Q.__select_class_id());
			}
		},500);
	}
});