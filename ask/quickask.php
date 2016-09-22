<!--在线提问  开始-->
               <div class="wmdoc_ask mTop20">
                <div class="ksAks mTop">
                  <div class="kotit"><span><img  src="http://ask.9939.com/ask_details/images/ltitm3.jpg"/></span><span class="qjd">（万名医生给您做疾病解答）</span></div>
                  <div class="xz_inpue clearfix">
                    <div class="mRit36 mH10 fl">
                     <b class="fl">性  别：</b>                          
                     <div class="mcko fl">
                      <span class="fl"><input type="radio" class="w14 fl"  name="sex"  value="0"><label class="nan fl">男</label></span>
                      <span class="clob5 fl">|</span>
                      <span class="fl"><input type="radio" class="w14 fl"  name="sex"  value="1"><label class="women fl" >女</label></span></div>
                    </div>
                    <div class="mRit36 fl">
                      <b class="mH10 fl">年  龄：</b><div class="ques_lined5 fl">
                      <input type="hidden" name="agesel" id="agesel" value="岁">
                      <input type="text" style="color:#999" class="quesage" value="如：28" 
                      onfocus="if(this.value=='如：28'){this.value=''};this.style.color='#333';"
                      onblur="if(this.value==''||this.value=='如：28'){this.value=如：28';this.style.color='#999';}">
                      <div class="showage fr mp">
                        <span class="ksui">岁</span>
                        <div class="ageLx rol" style="display:none;">
                         <a >岁</a>
                         <a >月</a>
                         <a >天</a>
                       </div>
                     </div>
                   </div>
                 </div>
                 <div class="mRit36 fl">
                   <b class="mH10 fl">选择科室：</b><div class="ques_lined5 quesw162 ques_xla fl">
                   <input type="text" style="color:#999" class="quit_writ" value="请您选择科室！" 
                   onfocus="if(this.value=='请您选择科室！'){this.value=''};this.style.color='#333';" 
                   onblur="if(this.value==''||this.value=='请您选择科室！'){this.value=如：28';this.style.color='#999';}">
                   <div class="showage ks_show fr" style="z-index:1000;"></div>
                   <!--科室弹框 开始-->
                   <div class="ksxl dopc1">
                     <div class="choks pl18"><p class="ple_ks">请选择科室</p><p  class="cho_zj">（选择科室专家能更快为您解答）</p>
                      <div class="xlerr"><a><img  src="http://ask.9939.com/ask_details/images/xlerr.png"/></a></div></div>
                      <div class="ks_xlab pl18 mTop8"><b class="cko_wrblue">您选择的科室：</b><a href="" title="">内科</a>                               &gt;<a  href="" title="">神经内科</a>&gt;<a href="" title="">头疼</a></div>
                      <div class="choks_mian clearfix">
                        <!--一级科室 开始-->
                        <div class="choksone  chosame choks_line mTop12"><h2>一级科室</h2>
                         <div class="kstw_a  kstw_one mTop12">
                          <?php
                            foreach ($this->dzjb as $k => $v) {
                              echo '<a class="keshi1" id="'.$k.'" title="'.$v["name"].'" >'.$v["name"].'</a>';
                            }
                          ?>
                        </div></div>
                         <!--一级科室 结束--> 
                         <!--二级科室 开始-->
                         <div class="bigTwo">
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 kstw_two"><a title="心血管内科" class="active">心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div> 
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" >心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2>       <div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" class="active">心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" class="active">心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" class="active">心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2>       <div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" >心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 "><a  title="心血管内科" class="active">心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2>  <div class="kstw_a  mTop12 kstw_two"><a title="心血管内科" class="active">心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div> 
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" >心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" class="active">心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" class="active">心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" class="active">心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" >心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 "><a  title="心血管内科" class="active">心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" class="active">心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" class="active">心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" class="active">心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                           <div class="chokstwo chosame choks_line mTop12" style="display:none;"><h2>二级科室</h2><div class="kstw_a  mTop12 kstw_two"><a  title="心血管内科" >心血管内科</a><a  title="内分泌科">内分泌科</a><a  title="风湿科">风湿科</a><a  title="消化内科">消化内科</a><a  title="神经内科">神经内科</a><a  title="呼吸内科">呼吸内科</a><a  title="血液科">血液科</a><a  title="肾内科">肾内科</a><a  title="内科其他">内科其他</a></div></div>
                         </div>
                         <!--二级科室 结束--> 
                         <!--三级科室 开始-->
                         <div class="bigTre">
                           <div class="chokstree chosame mTop12" style=" display:none;"><h2>相关疾病</h2><div class="kstw_a  mTop12"><a  title="头疼" class="active">头疼</a><a  title="癫痫">癫痫</a><a  title="脑梗塞">脑梗塞</a><a  title="神经衰弱">神经衰弱</a><a  title="坐骨神经痛">坐骨神经痛</a><a  title="昏迷">昏迷</a><a  title="神经 ">神经 </a><a  title="内科其它">内科其它</a></div></div>
                           <div class="chokstree chosame mTop12" style=" display:none;"><h2>相关疾病</h2><div class="kstw_a  mTop12"><a  title="头疼" class="active">头疼</a><a  title="癫痫">癫痫</a><a  title="脑梗塞">脑梗塞</a><a  title="神经衰弱">神经衰弱</a><a  title="坐骨神经痛">坐骨神经痛</a><a  title="昏迷">昏迷</a><a  title="神经 ">神经 </a><a  title="内科其它">内科其它</a></div></div>
                           <div class="chokstree chosame mTop12" style=" display:none;"><h2>相关疾病</h2><div class="kstw_a  mTop12"><a  title="头疼" class="active">头疼</a><a  title="癫痫">癫痫</a><a  title="脑梗塞">脑梗塞</a><a  title="神经衰弱">神经衰弱</a><a  title="坐骨神经痛">坐骨神经痛</a><a  title="昏迷">昏迷</a><a  title="神经 ">神经 </a><a  title="内科其它">内科其它</a></div></div>
                           <div class="chokstree chosame mTop12" style=" display:none;"><h2>相关疾病</h2><div class="kstw_a  mTop12"><a  title="头疼">头疼</a><a  title="癫痫">癫痫</a><a  title="脑梗塞">脑梗塞</a><a  title="神经衰弱">神经衰弱</a><a  title="坐骨神经痛">坐骨神经痛</a><a  title="昏迷">昏迷</a><a  title="神经 ">神经 </a><a  title="内科其它">内科其它</a></div></div>
                           <div class="chokstree chosame mTop12" style=" display:none;"><h2>相关疾病</h2><div class="kstw_a  mTop12"><a  title="头疼">头疼</a><a  title="癫痫">癫痫</a><a  title="脑梗塞">脑梗塞</a><a  title="神经衰弱">神经衰弱</a><a  title="坐骨神经痛">坐骨神经痛</a><a  title="昏迷">昏迷</a><a  title="神经 ">神经 </a><a  title="内科其它">内科其它</a></div></div>
                           <div class="chokstree chosame mTop12" style=" display:none;"><h2>相关疾病</h2><div class="kstw_a  mTop12"><a  title="头疼">头疼</a><a  title="癫痫">癫痫</a><a  title="脑梗塞">脑梗塞</a><a  title="神经衰弱">神经衰弱</a><a  title="坐骨神经痛">坐骨神经痛</a><a  title="昏迷">昏迷</a><a  title="神经 ">神经 </a><a  title="内科其它">内科其它</a></div></div>
                           <div class="chokstree chosame mTop12" style=" display:none;"><h2>相关疾病</h2><div class="kstw_a  mTop12"><a  title="头疼">头疼</a><a  title="癫痫">癫痫</a><a  title="脑梗塞">脑梗塞</a><a  title="神经衰弱">神经衰弱</a><a  title="坐骨神经痛">坐骨神经痛</a><a  title="昏迷">昏迷</a><a  title="神经 ">神经 </a><a  title="内科其它">内科其它</a></div></div>
                           <div class="chokstree chosame mTop12" style=" display:none;"><h2>相关疾病</h2><div class="kstw_a  mTop12"><a  title="头疼">头疼</a><a  title="癫痫">癫痫</a><a  title="脑梗塞">脑梗塞</a><a  title="神经衰弱">神经衰弱</a><a  title="坐骨神经痛">坐骨神经痛</a><a  title="昏迷">昏迷</a><a  title="神经 ">神经 </a><a  title="内科其它">内科其它</a></div></div>
                           <div class="chokstree chosame mTop12" style=" display:none;"><h2>相关疾病</h2><div class="kstw_a  mTop12"><a  title="头疼">头疼</a><a  title="癫痫">癫痫</a><a  title="脑梗塞">脑梗塞</a><a  title="神经衰弱">神经衰弱</a><a  title="坐骨神经痛">坐骨神经痛</a><a  title="昏迷">昏迷</a><a  title="神经 ">神经 </a><a  title="内科其它">内科其它</a></div></div>
                           <div class="chokstree chosame mTop12" style=" display:none;"><h2><a  title="相关疾病">二级科室 </a></h2><div class="kstw_a  mTop12"><a  title="头疼">头疼</a><a  title="癫痫">癫痫</a><a  title="脑梗塞">脑梗塞</a><a  title="神经衰弱">神经衰弱</a><a  title="坐骨神经痛">坐骨神经痛</a><a  title="昏迷">昏迷</a><a  title="神经 ">神经 </a><a  title="内科其它">内科其它</a></div></div>
                         </div> 
                         <!--三级科室 结束-->                            
                       </div>
                       <div class="chock pl18 mTop12"><span class="cho_bqc"></span><span><img src="http://ask.9939.com/ask_details/images/ksx_pic.jpg" alt="" title="我不清楚选择哪个科室"/></span>  </div>
                       <p class="cho_confirm">确 定</p>
                     </div>
                     <!--科室弹框结束-->
                   </div>
                 </div>
               </div>                               
               <div class="ktex mTop15">
                <h2>请概括描述您的病情及疑问</h2>
                <div class="txa ms_tit">
                  <textarea class="asktex_d " placeholder="请简述您的问题作为标题"></textarea>
                  <p class="fr asknum">5/20</p>
                </div>
              </div>
              <div class="ktex mTop15">
               <h2>请详细描述您的病情症状</h2>
               <div class="txa twnose">
                <textarea  class="asktex_b" placeholder="描述越清楚，医生回答越详细"></textarea>
                <p class="fr asknum">5/500</p>
              </div>
            </div>
            <div class="yzcl mTop15"><div class="fl"><b class="mH10 fl">验 证 码：</b><div class="ques_lined5 ques_w55 fl"><input type="text"  class="quesage"/></div><div class="pic_yzm fl"><img src="http://ask.9939.com/ask_details/images/pic_yzm.jpg"/></div></div></div>
            <div class="qask qaskce mwTop14"><input type="submit" value=""></div>
          </div>
        </div>
        <!--在线提问  结束-->
         <script type="text/javascript">
   $(".kstw_a  a").click(function(){
       $(this).addClass("color").siblings().removeClass();})
  //输入框
    $(function () {  
            $(".asktex_b").focus(function () {  
                $('.twnose').addClass("focus");  
            }).blur(function () {  
                $('.txa').removeClass("focus");  
            });  
        });  
      $(function () {  
            $(".asktex_d").focus(function () {  
                $('.ms_tit').addClass("focus");  
            }).blur(function () {  
                $('.txa').removeClass("focus");  
            });  
        });  
  
  //选择年龄
    $('.ksui').click(function(){
        $(this).next().toggle();
      })
      $('.ageLx a').click(function(){
        $('.ksui').html($(this).html());
        $('#agesel').val($(this).html());
        $(this).parent().hide();
      })
  //选择科室
  $('.ks_show').click(function(){
    $('.ksxl').show();
    })
  $('.xlerr a,.cho_confirm').click(function(){
    $('.ksxl').hide();  
  });
     $('.cho_bqc').click(function(){
    if($(this).attr('checked')!='checked'){
    $(this).css({'background':'#f9f9f9','border':'1px solid #d5d5d5'}).html('√'); $(this).attr('checked','checked');  
    }
    else{
    $(this).css({'background':'none','border':'1px solid #d5d5d5'}).html(null);$(this).removeAttr('checked');}
  });
    $('.choksone .kstw_one a').click(function(){
      $(this).addClass('active').siblings().removeClass('active');
    $('.chokstwo').hide().eq($(this).index()).show();
  });
  $('.chokstwo .kstw_two a').click(function(){
      $(this).addClass('active').siblings().removeClass('active');
    $('.chokstree').hide().eq($(this).index()).show();
  });
  $('.chokstree .kstw_two a').click(function(){
      $(this).addClass('active').siblings().removeClass('active');
  });
     </script>