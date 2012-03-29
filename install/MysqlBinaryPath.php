<?php

class MysqlBinaryPath
	extends InstallerBase
{
	
	public function IsInstalled()
	{
		return isset($this->config->mysqlBinaryPath);
	}
	
	public function Install()
	{
		$path = $_REQUEST['mysqlBinaryPath'];
		$path = rtrim($path, '\/');
		$this->config->mysqlBinaryPath = $path . DIRECTORY_SEPARATOR;
	}
	
	
	public function RenderForm()
	{
		return 	"<lable>Path to MySQL Binary:</lable>&nbsp;" . 
				"<input type='text' name='mysqlBinaryPath' />";
	}
	
}