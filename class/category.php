<?php
/**
 * category.php 栏目模块
 * @author xiongzhixin (xzx747@sohu.com) 2007-06-25
 */
//define("OPEN_DEBUG",true);
require_once("../class/common.inc.php");
$sAction = isset($_REQUEST["act"]) ? $_REQUEST["act"] : "listAll";
checkExecPower("category", $sAction);		//权限检查

loadLib("category");
$sTbl_1 = isset($_GET['a']) ? $_GET['a'] : "9939_buwei_category";
$sTbl = $tablepre.$sTbl_1;
$aTbl = array("9939_buwei_category"=>"部位","9939_drug_category"=>"药品","9939_section_category"=>"科室","9939_disease_category"=>"疾病");

$sCatName = $aTbl[$sTbl_1];

$tpl->assign("PAGE_FUNC_BIG_LINK", "?a=$sTbl_1");
$tpl->assign("PAGE_FUNC_BIG_NAME", "栏目管理");
$tpl->assign("sTbl",$sTbl_1);

if($sAction == "add") // 栏目添加
{
	$pID = isset($_GET['pID']) ? $_GET['pID'] : 0;
	$tpl->assign("PAGE_FUNC_SMALL_NAME","添加".$sCatName."栏目");	
	$tpl->assign("pID",$pID);
	$tpl->assign("sOptions",getOptions($sTbl_1,$pID));
	$tpl->display("admin/category_add.tpl.htm");
}
elseif($sAction == "addSave") //栏目添加保存
{	
	$aField['name'] = $_POST['name'];
	$aField['intro'] = $_POST['intro'];
	$aField['pID'] = $_POST['pID'];
	$aField['addDate'] = date("Y-m-d H:i:s");
	$id = $db->insert($sTbl,$aField);
		
	if($id > 0)
	{
		$aField['orderID'] = $id;
		$db->update($sTbl,$aField,"id=$id");
		//echo $sTbl."<br>".$sTbl_1; exit;
		$sUrl = "?act=createOptions&a={$sTbl_1}&layer=3&comeUrl=?act=addandtbl={$sTbl_1}andpID=".$_POST['pID']; // 添加后自动生成文件
		//echo $sUrl;exit;
		redirect($sUrl,1,"添加成功！");
		
	}
	else
	{
		//redirect("?act=add&a=$sTbl&pID=".$_POST['pID'],5,"添加失败！");
	}
}
elseif($sAction == "edit" && isset($_GET['id'])) // 栏目修改
{
	$id = $_GET['id'];
	$tpl->assign("PAGE_FUNC_SMALL_NAME", "修改".$sCatName."栏目");
	$aField = $db->getRecordSet("SELECT * FROM $sTbl WHERE id=$id",1);
	
	$tpl->assign("aField",$aField);
	$tpl->assign("sOptions",getOptions($sTbl_1,$aField['pID']));	
	$tpl->display("admin/category_edit.tpl.htm");
}
elseif($sAction == "editSave" && isset($_POST['id'])) // 栏目修改保存
{
	$id = $_POST['id'];
	$aField['name'] = $_POST['name'];
	$aField['intro'] = $_POST['intro'];
	$aField['pID'] = $_POST['pID'];
	$aField['addDate'] = date("Y-m-d H:i:s");
	if($db->update($sTbl,$aField,"id=$id"))
	{
		$sUrl = "?act=createOptions&a={$sTbl_1}&layer=3&comeUrl=?a={$sTbl_1}andpID=".$_POST['pID']; // 添加后自动生成文件
		//echo $sUrl;exit;
		redirect($sUrl,1,"修改成功！");
	}	
	else
		redirect("?a=$sTbl",5,"修改失败！");
}
elseif($sAction == "del" && isset($_GET['id']))  // 栏目删除
{
	$id = $_GET['id'];
	
	$sWhere = "pID=$id";
	$iSonNum = $db->getRowsNum("SELECT COUNT(id) FROM $sTbl WHERE $sWhere");	
	
	if($iSonNum==0)
	{
		$aTemp = $db->getRecordSet("select pID from $sTbl where id=$id",1);
		if($db->query("delete FROM $sTbl WHERE id = $id"))		
		{
			$sUrl = "?act=createOptions&a={$sTbl_1}&layer=3&comeUrl=?a={$sTbl_1}andpID=".$aTemp['pID']; // 添加后自动生成文件
			//echo $sUrl;exit;
			redirect($sUrl,1,"删除成功！");
		}
		else
			redirect("?a=$sTbl",5,"删除失败！");
	}
	else 
	{
		redirect("?a=$sTbl",5,"存在子类，不能直接删除！");
	}
}
elseif($sAction == "listAll") // 栏目列表
{
	$tpl->assign("PAGE_FUNC_SMALL_NAME", $sCatName."栏目列表");
	$pID = isset($_GET['pID'])? $_GET['pID'] : 0;		
	$aList = category::getSubList($sTbl_1,$pID,true);
	for($i=0; $i<count($aList); $i++)	
	{
		$id = $aList[$i]['id'];
		if($pID == 0 || $pID == $id) 
  			$aList[$i]['name'] = "<a href='?a=$sTbl_1&pID=$id'><b>".$aList[$i]['name']."</b></a>";
  		else 
  			$aList[$i]['name'] = "&nbsp;&nbsp;&raquo;&nbsp;<a href='?a=$sTbl_1&pID=$id'>".$aList[$i]['name']."</a>";  		
	}	
	//print_r($aList);	
	$tpl->assign("aList",$aList);	
	$tpl->assign("pID",$pID);
	$tpl->display("admin/category_list.tpl.htm");
}
elseif($sAction == "changeOrder" && $_POST['orderID']) // 更改排序
{
	$pID = (isset($_GET['pID'])) ? $_GET['pID'] : 0;
	$aOrderID = $_POST['orderID'];
	$aID 	  = $_POST['id'];	
	
	for($i=0; $i<count($aOrderID); $i++)
	{
		$aField['orderID'] = $aOrderID[$i];
		$db->update($sTbl,$aField,"id=".$aID[$i]);
	}
	//exit;
	redirect("?pID=$pID",3,"更改成功！");
}
elseif($sAction == "createOptions" && isset($_GET['layer']))
{

	  
	/**********************************   生成栏目缓存   *************************************/
	$array = array('buwei'=>'9939_buwei_category', 'area'=>'9939_area', 'drug'=>'9939_drug_category','section'=>'9939_section_category','best_ks'=>'9939_section_category');
	$array2array = array();
	foreach($array as $key => $value)
	{
		$array2array[$key] = array();
		$sql = "SELECT * FROM $value order by pID asc,id asc";
		$result = $db->query($sql);
		$str = '';
		while($r = mysql_fetch_object($result))
		{
			//echo $r->pID,'--',$r->id,"<br>";
			if(!isset($old_id))
			{
				$old_id = $r->pID;
				$str = '<? $sOptions="';
			}
			$array2array[$key][$r->pID] = array();
			$array2array[$key][$r->pID][$r->id] = '<option value='.$r->id.'>'.$r->name.'</option>';
			if($old_id != $r->pID)
			{
				//echo $old_id,"<br>";
				$str .= $newstr .'"; ?>';
				chdir('../include_dzjb');
				file_put_contents('cat_'.$key.'_'.$old_id.'.js', $newstr);
				file_put_contents('cat_'.$key.'_'.$old_id.'_options.php', $str);
				//echo $str;
				$str = '<? $sOptions="';
				$old_id = $r->pID;
				$newstr = '';
			}
			$newstr .= '<option value='.$r->id.' id=sec_'.$r->id.'>'.$r->name.'</option>';
		}
		
	}
	/**********************************   生成栏目缓存   *************************************/
	
	
	/**********************************   生成hosp缓存   *************************************/
		unset($str);
		unset($old_id);
		//$sql = "SELECT distinct a.id, a.areaid, a.title, a.catid FROM 9939_dzjb a, 9939_area b where a.areaid=b.id and a.catid='1787' and a.areaid !=0 order by b.pID asc,b.id asc";
		$sql = "SELECT a.id, b.pID as areaid, a.title, a.catid FROM 9939_dzjb a, 9939_area b where a.areaid = b.id and a.catid='1787' and areaid in (select arrchildid from 9939_area order by pID asc,id asc ) order by b.pID asc,b.id asc";
		$result = $db->query($sql);
		$str = '';
		while($r = mysql_fetch_object($result))
		{
			if($r->catid != '1787')
			{
				continue;
			}
			//echo $r->areaid,'--',$r->id,"<br>";
			if(!isset($old_id))
			{
				$old_id = $r->areaid;
				$str = '<? $sOptions="';
			}
			$array2array[$key][$r->areaid] = array();
			$array2array[$key][$r->areaid][$r->id] = '<option value='.$r->id.'>'.$r->title.'</option>';
			if($old_id != $r->areaid)
			{
				//echo $old_id,"<br>";
				$str .= $newstr .'"; ?>';
				chdir('../include_dzjb');
				file_put_contents('hosp_'.$old_id.'.js', $newstr);
				file_put_contents('hosp_'.$old_id.'_options.php', $str);
				//echo $str;
				$str = '<? $sOptions="';
				$old_id = $r->areaid;
				$newstr = '';
			}
			$newstr .= '<option value='.$r->id.'>'.$r->title.'</option>';
		}
	/**********************************   生成doctor缓存   *************************************/
	echo mysql_num_rows($result);


	$iLayer = intval($_GET['layer']) - 1;
	if($iLayer < 0) exit;	
	$sOptions = "<?\n\$sOptions=\"";	
	//echo $sTbl_1; exit;
	$sOptions .= category::getOptions($sTbl_1,$iLayer);	 // -1表示无限级分类； 0表示一级分类； 1表示二级分类；以此类推	
	$sOptions .= "\n\";\n?>";
	$sFileName = "../include_dzjb/{$sTbl_1}_options.php";
	$fp = fopen($sFileName,"w");	
	if(fwrite($fp,$sOptions))
	{	
		$sUrl = isset($_GET['comeUrl']) ? str_replace("and","&",$_GET['comeUrl']) : "?a=$sTbl_1";	
		//echo "<br>".$sFileName."<br>"; echo $sUrl;exit;		
		redirect($sUrl,3,"生成成功！");
	}	
	else 
	{
		redirect("?a=$sTbl",5,"生成失败！");
	}
}
else
{
	showError("参数错误！");
}
?>