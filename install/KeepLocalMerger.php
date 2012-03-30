<?php

class KeepLocalMerger 
	extends InstallerBase
{
	
	public function IsInstalled()
	{
		$retval = array();
		exec("git config --get merge.keepLocal.name", $retval);
		
		return in_array("KeepLocal", $retval);
		
	}
	
	public function Install()
	{
		exec('git config merge.keepLocal.name "KeepLocal"');
		exec('git config merge.keepLocal.driver "echo \'Resolved conlict by keeping local file.\'"');
	}
	
	public function RenderForm()
	{
		return "";
	}
	
}