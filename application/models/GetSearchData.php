<?php
/**
 * 使用此类得先初始化数据 SetXmlData()
 *
 */
class GetSearchData{
	/**
	   *##############################################
	   * @FILE_NAME :GetSearchData.php
	   *##############################################
	   *
	   * @author : 张泽华
	   * @MailAddr : zhang-zehua@163.com
	   * @copyright : Copyright (c) 2009 中视在线(http://www.9939.com)
	   * @PHP Version :  Ver 5.21
	   * @Apache  Version : Ver 2.20
	   * @MYSQL Version : Ver 5.0
	   * @Version : Sun Sep 27 14:16:13 CST 2009ead 1.0
	   * @DATE : Sun Sep 27 14:16:13 CST 2009
	   *
	   *==============================================
	   * @Desc :  处理搜索返回的xml数据
	   *==============================================
	   */ 

	/**
	 * 设置初始xml数据
	 *
	 * @param xml $xmlData
	 */
	public function SetXmlData($xmlData){
		$this->xmldata = $xmlData;
	}

	/**
	 * 处理原始xml数据
	 *
	 * @param xml $xmlData
	 * @return array
	 */
	public function DoXmlData(){
		$p = xml_parser_create();
		xml_parse_into_struct($p, $this->xmldata, $values, $index);
		xml_parser_free($p);
		$tmp_arr = array();
		$i = 1;
		$j = 0;
		
		foreach((array) $values as $v){
			if($v['level']<4){
				$tmp_arr[$v['level']][$v['tag']] = $v;
			}elseif($v['level']==4){
				$tmp_arr[$v['level']][$j][$v['tag']] = $v['value'];
				if($i%5==0) $j++;
				$tmp_arrtt[$v['level']][$i][$v['tag']] = $v['value'];
				$i++;
			}
		}
//print_r($tmp_arrtt);exit;
		return $tmp_arr;
	}

	/**
	 * 获取总的结果数
	 *
	 * @return int
	 */
	public function GetTotal(){
		$tmp_xml_arr = $this->DoXmlData();
		$tmp_total   = $tmp_xml_arr[2]['TOTAL']['value'];
		return $tmp_total;
	}

	/**
	 *  获取搜索所花的时间
	 *
	 * @return int
	 */
	public function GetSearchTime(){
		$tmp_xml_arr = $this->DoXmlData();
		$tmp_elapsed   = $tmp_xml_arr[2]['ELAPSED']['value'];
		return $tmp_elapsed;
	}

	/**
	 * 获取每页条数
	 *
	 * @return int
	 */
	public function GetPageSize(){
		$tmp_xml_arr = $this->DoXmlData();
		$tmp_pagesize   = $tmp_xml_arr[2]['PAGESIZE']['value'];
		return $tmp_pagesize;
	}

	/**
	 * 获取搜索列表
	 *
	 * @return array
	 */
	public function GetList(){
		$tmp_xml_arr = $this->DoXmlData(); 
		$tmp_list    = $tmp_xml_arr[4]; 
		return $tmp_list;
	}

	/**
	 * 获取结果分页
	 *
	 * @param int $page_now 当前页
	 */
	public function GetPageHtml($page_now=1)
	{
		//总的页数
		$this->tpage  = ceil($this->GetTotal()/$this->GetPageSize());
		$current      = $page_now;
		if ($current>$this->tpage) {$current = $this->tpage;}
		if ($current<1) {$current = 1;}

		$this->curr  = $current;
		$this->psize = $this->GetPageSize();
		$this->file  = $this->file?$this->file:$_SERVER['PHP_SELF'];
		$this->pvar  = "page";

		if ($this->tpage > 1) {
			$this->output.="共". $this->GetTotal()." 条记录&nbsp;&nbsp;";
			//$this->output.="当前:第 $this->curr/$this->tpage 页&nbsp;&nbsp;";
			if ($current==1) {
				$this->output.='首页&nbsp;';
				$this->output.='前一页&nbsp;';
			}else
			{
				$this->output.='<a href='.$this->file.'/'.$this->pvar.'/1'.($this->varstr).' title="首页"><font  color=#FF0000>首页</font></a>&nbsp;';
				$this->output.='<a href='.$this->file.'/'.$this->pvar.'/'.($current-1).($this->varstr).' title="前一页"><font  color="#FF0000">前一页</font></a>&nbsp;';
			}

			$start	= floor($current/10)*10;
			$end	= $start+9;

			if ($start<1)			{$start=1;}
			if ($end>$this->tpage)	{$end=$this->tpage;}

			for ($i=$start; $i<=$end; $i++) {
				if ($current==$i) {
					$this->output.='<font color="red"><U>['.$i.']</U></font>&nbsp;';    //输出当前页数
				} else {
					$this->output.='<a href="'.$this->file.'/'.$this->pvar.'/'.$i.$this->varstr.'">['.$i.']</a>&nbsp;';    //输出页数
				}
			}
			if ($this->tpage==$current ) {
				$this->output.='下一页&nbsp;';
				$this->output.='末页';
			}
			else
			{
				$this->output.='<a href='.$this->file.'/'.$this->pvar.'/'.($current+1).($this->varstr).' title="下一页"><font  color="#FF0000">下一页</font></a>&nbsp;';
				$this->output.='<a href='.$this->file.'/'.$this->pvar.'/'.($this->tpage).($this->varstr).' title="末页"><font  color="#FF0000">末页</font></a>';
			}
			//			$this->output.="&nbsp;&nbsp;跳转到<select name=p onchange=\"javascript:this.document.location='".$this->file."/".$this->pvar."/'+(this.options[this.selectedIndex].value)+'".$this->varstr."'\">";
			//			$startpage=$this->curr-20;
			//			$endpage=$this->curr+20;
			//			if($startpage<1)				$startpage=1;
			//			if($endpage>$this->tpage)	$endpage=$this->tpage;
			//			for($i=$startpage;$i<=$endpage;$i++)
			//			{
			//				if($i==$this->curr)
			//				$this->output.="<option value=".$i." selected>第".$i."页</option>";
			//				else
			//				$this->output.="<option value=".$i.">第".$i."页</option>";
			//			}
			//			$this->output.="</select>";
		}
	}

	/**
	 * 获取页面传递的变量
	 *
	 */
	public function SetArr($data){
		foreach ((array)$data as $k=>$v) {
			$this->varstr.= "/".$k.'/'.urlencode($v);
		}
	}

	/**
     * 分页结果输出
     *
     * @access public
     * @param bool $return 为真时返回一个字符串，否则直接输出，默认直接输出
     * @return string
     */
	function output($return = false) {
		if ($return) {
			return $this->output;
		} else {
			echo $this->output;
		}
	}

	/**
	 * 用于分页设置路径
	 *
	 * @param str $url
	 */
	public function SetUrl($url){
		$this->file = $url;
	}

	/**
	 * 自定义获取字符
	 *
	 * @param str $str 需要处理的字串
	 * @param int $num 需要截取的个数 
	 * @return unknown
	 */
	function getstr($str,$num,$more=false)
	{
		$leng=strlen($str);
		if($num>=$leng) return $str;
		$str=preg_replace("#[\r\n\s]#is",' ',$str);
		$str=strip_tags($str);
		$word=0;
		$i=0;
		while($word!=$num)
		{
			if(ord($str[$i])>0xC3)
			{
				$re.=substr($str,$i,3);
				$i+=3;
				$word++;
			}
			else
			{
				$re.=substr($str,$i,1);
				$i++;
				$word++;
			}
		}
		if($more) $re .= "...";
		return $re;
	}
}
?>