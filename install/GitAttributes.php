<?php

class GitAttributes
	extends InstallerBase
{
	private $attributesFile = "../.gitattributes";
	
	public function IsInstalled()
	{
		$content = file_get_contents($this->attributesFile);
		
		return $content == $this->origFileContent();
	}
	
	public function Install()
	{
		file_put_contents($this->attributesFile, $this->origFileContent());
	}
	
	private function origFileContent()
	{
		$rows = array();
		$rows[] = "data/sql/eCamp3dev.sql merge=keepLocal";
		
		return implode(PHP_EOL, $rows);
	}
	
	public function RenderForm()
	{
		return "";
	}
	
}