<?php
/**
 * pic.lib.php（图片上传类）
 * @author xiongzhixin (xzx747@sohu.com) 2006.12
 */

class pic 
{
	var $sUploadPath;				//图片存储路径
	var $aWaterColor;            // 水印颜色
	var $toFile	= true;			//是否生成文件
	var $fontName;					//使用的TTF字体名称	
	var $useTimeAsFileName = true;	//是否使用时间做为上传后的文件名
	
	function __construct($sUploadPath,$aWaterColor="",$sFontPath="")
	{
		$this->sUploadPath	= $sUploadPath;
		$this->aWaterColor = $aWaterColor;		
		$this->fontName		= ($sFontPath) ? $sFontPath."1.ttf" : $sUploadPath."1.ttf";				
	}
	
	/**
	 * 文件上传 (added by xzx 2007-11-01)
	 *
	 * @param string $sField 表单对象名称
	 * @param unknown_type $i 
	 * @return string
	 */
	
	function uploadPic($sField,$i=0)
	{
		$aFile = $_FILES[$sField];  // Array([name]=>231109440.BMP [type]=>image/bmp [tmp_name] => C:\WINNT\TEMP\php8D.tmp    [error] => 0    [size] => 233794)		
		if($aFile['name'])
		{
			$a = pathinfo($aFile["name"]); // Array([dirname] => . [basename] => 231109440.BMP [extension] => BMP)			
			$sExt = $a['extension'];
			$y = date('Y');
			
			
			
			
			if(!is_dir($this->sUploadPath.$y)) mkdir($this->sUploadPath.$y,0777);
			$sDir = $y.'/'.date("md");
			if(!is_dir($this->sUploadPath.$sDir)) mkdir($this->sUploadPath.$sDir,0777);
						

			

			if ($this->useTimeAsFileName) 
			{
				$sFileName = $this->sUploadPath.$sDir."/".date("dHis").$i.".".$sExt;	
			}
			else 
			{
				$sFileName = $this->sUploadPath.$sDir."/".$aFile['name'];
			}
			$sFilePath = $sFileName;
    		if(copy($aFile['tmp_name'],$sFilePath)) 
    			return substr($sFileName,3);
    		else 
    			return "";
		}
		else
		{
			return "";
		}		
	}	
	
	/**
 	* 获取图片信息
 	*
 	* @param string $sFileName 图片地址
 	* @return array
 	*/
	
	function getImgInfo($sFileName) 
	{
		$sTmpFileName = $this->sUploadPath . $sFileName;		
		$aTemp	= getimagesize($sTmpFileName);	// Array([0] => 629 [1] => 559 [2] => 6 [3] => width="629" height="559" [bits] => 32 [mime] => image/bmp)		
		$aInfo["width"]	= $aTemp[0];
		$aInfo["height"]= $aTemp[1];
		$aInfo["type"]	= $aTemp[2];
		$aInfo["name"]	= $sFileName;  //$aInfo["name"]	= basename($sFileName); // 传回不含路径的档案字串
		$aInfo["size"]  = filesize($sTmpFileName);
		return $aInfo;  // Array ( [width] => 629 [height] => 559 [type] => 6 [name] => 2007-11/021651350.BMP [size] => 1406498 )
	}
	
	//==========================================
	// 函数: makeThumb($sourFile,$width=128,$height=128) 
	// 功能: 生成缩略图(输出到浏览器)
	// 参数: $sourFile 图片源文件
	// 参数: $width 生成缩略图的宽度
	// 参数: $height 生成缩略图的高度
	// 返回: 0 失败 成功时返回生成的图片路径
	//==========================================
	/**
	 * 生成缩略图
	 *
	 * @param string $sFileName 上传后的文件
	 * @param string $iWidth 缩略图宽度
	 * @param string $iHeight 缩略图高度
	 * @return string  
	 */
	
	function makeThumb($sFileName,$iWidth=128,$iHeight=128)
	{
		$aInfo	= $this->getImgInfo($sFileName);	
		//print_r($aInfo); 
		$sTmpFileName = $this->sUploadPath . $sFileName;			
		$sNewFileName = substr($aInfo["name"], 0, -4) . "_t.".substr($aInfo['name'],-3);
						
		switch ($aInfo["type"])
		{
			case 1:	//gif
				$bSrc = imagecreatefromgif($sTmpFileName);
				break;
			case 2:	//jpg
				$bSrc = imagecreatefromjpeg($sTmpFileName);
				break;
			case 3:	//png
				$bSrc = imagecreatefrompng($sTmpFileName);
				break;
			default:
				return "";
				break;
		}
		
		if (!$bSrc) return "";		
		
		$iSrcW	= $aInfo["width"];
		$iSrcH	= $aInfo["height"]; 
		
		/**
		$iW  = ($iWidth > $iSrcW) ? $iSrcW : $iWidth;
		$iH = ($iHeight > $iSrcH) ? $iSrcH : $iHeight;
		
		if ($iSrcW * $iW > $iSrcH * $iH)
		{
			$iNewW = $iW;
			$iNewH = round($iSrcH * $iW / $iSrcW);
		}	
		else
		{
			$iNewW = round($iSrcW * $iH / $iSrcH);
			$iNewH = $iH;
		}		
		**/
		//echo "srcW=$iSrcW<br>srcH=$iSrcH<br>w=$iW<br>h=$iH<br>newW=$iNewW<br>newH=$iNewH";	三个宽度和高度
		
		$iNewW = $iWidth;
		$iNewH = $iHeight;		
		
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{			
			$bNew = imagecreatetruecolor($iNewW, $iNewH);
			ImageCopyResampled($bNew, $bSrc, 0, 0, 0, 0, $iNewW, $iNewH, $iSrcW, $iSrcH);
		}
		else
		{
			$bNew = imagecreate($iNewW, $iNewH);
			ImageCopyResized($bNew, $bSrc, 0, 0, 0, 0, $iNewW, $iNewH, $iSrcW, $iSrcH);
		}		
		//*/
        if ($this->toFile)
		{			
			if (file_exists($this->sUploadPath . $sNewFileName)) unlink($this->sUploadPath . $sNewFileName);
			ImageJPEG($bNew, $this->sUploadPath . $sNewFileName);
			ImageDestroy($bNew);
			ImageDestroy($bSrc);		
			return $sNewFileName;
		}
		else
		{
			ImageJPEG($bNew);
			ImageDestroy($bNew);
			ImageDestroy($bSrc);
		}
	}
	//==========================================
	// 函数: makeWaterMark($sourFile, $text)
	// 功能: 给图片加水印
	// 参数: $sourFile 图片文件名
	// 参数: $text 文本数组(包含二个字符串)
	// 返回: 1 成功 成功时返回生成的图片路径
	//==========================================
	
	function makeWaterMark($sFileName,$sWaterText,$iColor=0)
	{
		$aInfo	= $this->getImgInfo($sFileName);	
		$sTmpFileName = $this->sUploadPath . $sFileName;		
		$sNewFileName = substr($aInfo["name"], 0, -4) . "_w.".substr($aInfo['name'],-3);
		switch ($aInfo["type"])
		{
			case 1:	//gif
				$bSrc = imagecreatefromgif($sTmpFileName);
				break;
			case 2:	//jpg
				$bSrc = imagecreatefromjpeg($sTmpFileName);
				break;
			case 3:	//png
				$bSrc = imagecreatefrompng($sTmpFileName);
				break;
			default:
				return "";
				break;
		}
		if (!$bSrc) return "";
		
		$iSrcW	= $aInfo["width"];
		$iSrcH	= $aInfo["height"];
		$iNewW = $iSrcW; $iNewH = $iSrcH;
					
		//*
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{
			$bNew = imagecreatetruecolor($iNewW, $iNewH);
			ImageCopyResampled($bNew, $bSrc, 0, 0, 0, 0, $iNewW, $iNewH, $iSrcW, $iSrcH);
		}
		else
		{
			$bNew = imagecreate($iNewW, $iNewH);
			ImageCopyResized($bNew, $bSrc, 0, 0, 0, 0, $iNewW, $iNewH, $iSrcW, $iSrcH);
		}	
		
		$iAlpha = 63; // 半透明
		$red = imageColorAllocateAlpha($bNew, 255, 0, 0,$iAlpha);      //红色
		$green = imagecolorallocatealpha($bNew,0,255,0,$iAlpha);       // 绿色
		$blue = imagecolorallocatealpha($bNew,0,0,255,$iAlpha);       // 蓝色
		$white = imagecolorallocatealpha($bNew,255,255,255,$iAlpha);  // 白色
		$black = imagecolorallocatealpha($bNew,0,0,0,$iAlpha);       // 黑色
		$grey = imagecolorallocatealpha($bNew,192,192,192,$iAlpha);  // 灰色
		$purple = imagecolorallocatealpha($bNew,255,0,255,$iAlpha); // 紫色
		
		// 和imagecolorallocate() 相同，但多了一个额外的透明度参数,其值从0 到127。0 表示完全不透明，127 表示完全透明。
		$aWaterColor = $this->aWaterColor;
		$sColor = $aWaterColor[$iColor];
		// print_r($aWaterColor); echo $sColor; 	exit;
		$color = $$sColor;	
		//echo  $this->fontName;
		
		@ImageTTFText($bNew, 18, 0, 5, 23, $color, $this->fontName, $sWaterText);  // 加水印文字1 （左上角）
		@ImageTTFText($bNew, 18, 0,$iNewW/2-80, $iNewH/2-5, $color, $this->fontName, $sWaterText);  // 加水印文字1 （中间）
		@ImageTTFText($bNew, 18, 0,$iNewW-160, $iNewH-5, $color, $this->fontName, $sWaterText);  // 加水印文字1 （右下角）
		// array imagettftext ( resource image, int size, int angle, int x, int y, int color, string fontfile, string text)
		// 将字符串 text 画到 image 所代表的图像上，从坐标 x，y（左上角为 0, 0）开始，角度为 angle，颜色为 color，使用 fontfile 所指定的 TrueType 字体文件。根据 PHP 所使用的 GD 库的不同，如果 fontfile 没有以 '/'开头，则 '.ttf' 将被加到文件名之后并且会搜索库定义字体路径
				
        if ($this->toFile)
		{
			//echo $this->sUploadPath . $sNewFileName;
			if (file_exists($this->sUploadPath . $sNewFileName)) unlink($this->sUploadPath . $sNewFileName);
			ImageJPEG($bNew, $this->sUploadPath . $sNewFileName);
			ImageDestroy($bNew);
			ImageDestroy($bSrc);

			return $sNewFileName;
		}
		else
		{
			ImageJPEG($bNew);
			ImageDestroy($bNew);
			ImageDestroy($bSrc);
		}
	}	
}
?>