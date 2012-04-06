<?php

class GitHooks
	extends InstallerBase
{
	private $preCommit 		= "../.git/hooks/pre-commit";
	private $postCheckout 	= "../.git/hooks/post-checkout";
	
	public function IsInstalled()
	{
		return file_exists($this->preCommit) && file_exists($this->postCheckout);
	}
	
	public function Install()
	{
		$path = null;
		
		if(array_key_exists('mysqlBinaryPath', $_REQUEST) && $_REQUEST['mysqlBinaryPath'] != "")
		{
			$path = $_REQUEST['mysqlBinaryPath'];
			$path  = rtrim($path, '\/');
			$path .= DIRECTORY_SEPARATOR;
		}
		elseif(!is_null($this->config->mysqlBinaryPath))
		{
			$path = $this->config->mysqlBinaryPath;
		}
		
		file_put_contents($this->preCommit, $this->origPreCommit($path));
		file_put_contents($this->postCheckout, $this->origPostCheckout($path));
		
		chmod($this->preCommit, 0755);
		chmod($this->postCheckout, 0755);
	}
	
	private function origPreCommit($path)
	{
		$rows 	= array();
		$rows[] = '#!/bin/bash';
	
		if(!is_null($path))
		{	$rows[] = 'export PATH=' . $path . ':$PATH';	}
		
		$rows[] = 'source bin/hooks/pre-commit.sh';
		$rows[] = 'exit 0';
		
		return implode(PHP_EOL, $rows);
	}
	
	private function origPostCheckout($path)
	{
		$rows 	= array();
		$rows[] = '#!/bin/bash';
		
		if(!is_null($path))
		{	$rows[] = 'export PATH=' . $path . ':$PATH';	}
		
		$rows[] = 'source bin/hooks/post-checkout.sh';
		$rows[] = 'exit 0';
		
		return implode(PHP_EOL, $rows);
	}
	
	public function RenderForm()
	{
		if(isset($this->config->mysqlBinaryPath))
		{
			return "";
		}
		
		return 	"<lable>Path to MySQL Binary:</lable>&nbsp;" .
				"<input type='text' name='mysqlBinaryPath' />";
	}
	
}