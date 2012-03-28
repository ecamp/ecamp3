<?php

class PhpBinaryPath
	extends InstallerBase
{
	
	public function IsInstalled()
	{
		return isset($this->config->phpBinaryPath);
	}
	
	public function Install()
	{
		$path = $_REQUEST['phpBinaryPath'];
		$path = rtrim($path, '/');
		$this->config->phpBinaryPath = $path . '/';
	}
	
	
	public function RenderForm()
	{
		return 	"<lable>Path to PHP Binary:</lable>&nbsp;" . 
				"<input type='text' name='phpBinaryPath' />";
	}
	
}