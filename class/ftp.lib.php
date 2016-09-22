<?php
class ftp
{
	//发送到远程的ftp上去，ftp服务器分发，需要php解析器支持ftp功能
	private $__bConnID;
	private $__bLoginResult;
	private $__sFtpServer;
	private $__sFtpUserName;
	private $__sFtpPwd;
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $sFtpServer
	 * @param unknown_type $sFtpUserName
	 * @param unknown_type $sFtpPwd
	 */
	function __construct($sFtpServer,$sFtpUserName,$sFtpPwd)
	{
		$this->__sFtpServer 	= $sFtpServer;
		$this->__sFtpUserName 	= $sFtpUserName;
		$this->__sFtpPwd	 	= $sFtpPwd;	
		$this->__connect();	
		$this->_login();
	}
	
	/**
	 * Enter description here...
	 *
	 */
	private function __connect()
	{
		$this->__bConnID = ftp_connect($this->__sFtpServer);
	}
	
	private function __close()
	{
		ftp_close($this->__bConnID);
	}
	
	private function _login()
	{		
		$this->__bLoginResult = ftp_login($this->__bConnID, $this->__sFtpUserName, $this->__sFtpPwd);		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $sSourceFile
	 * @param unknown_type $sDestinationFile
	 * @return ftp
	 */
	function ftpFile($sDestinationFile,$sSourceFile)
	{
		if(!$this->__bConnID)
			return "FTP连接失败！<br>";
		elseif (!$this->__bLoginResult)
			return "FTP连接失败!用户名或密码错误。！<br>";
		
		// upload the file
	    $bUpload = ftp_put($this->__bConnID,$sDestinationFile, $sSourceFile,FTP_BINARY);
	    
	    // check upload status
		if (!$bUpload)
			return "文件{$sSourceFile}发送到{$sDestinationFile},在{$this->__sFtpServer}上，操作失败！<br>";
	    else 
	    	return "文件{$sSourceFile}发送到{$sDestinationFile},在{$this->__sFtpServer}上，操作成功！<br>";
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $aSourceFile
	 * @param unknown_type $aDestinationFile
	 * @return unknown
	 */
	function ftpBatchFile($aSourceFile,$aDestinationFile)
	{
		for ($i = 0; $i < count($aSourceFile); $i++)
			$this->ftpFile($aSourceFile[$i],$aDestinationFile[$i]);
		return true;
	}
	
	/**
	 * 新建目录
	 *
	 * @param unknown_type $sDIRName
	 * @return unknown
	 */
	function ftpMkDIR($sDIRName)
	{
		$aDIRArray = explode("/", $sDIRName);
			
		foreach ($aDIRArray as $rr)
		{
			if($rr == "" || $rr == "/")
				continue;
			//如果当前目录存在，先进去，否则就创建
			$isSucc = ftp_chdir($this->__bConnID, $rr);
			if(!$isSucc)
				return ftp_mkdir($this->__bConnID, $rr);			
		}
	}
	
	private  function ftpMkOneDir($sDIRName)
	{
		return ftp_mkdir($this->__bConnID, $sDIRName);
	} 
	
	/**
	 * 远程文件删除
	 *
	 */
	function ftpDelete($sFileName)
	{
		return ftp_delete($this->__bConnID,$sFileName);
	}
	
	/**
	 * Enter description here...
	 *
	 */
	function __destruct()
	{
		$this->__close();
	}
}
?>