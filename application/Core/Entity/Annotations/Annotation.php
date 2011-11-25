<?php

namespace Core\Entity\Annotations;

abstract class Annotation extends \Doctrine\Common\Annotations\Annotation
{
	public $type = null;
	
	private $doc;
	
	protected function setDoc($doc)
	{
		$this->doc = $doc;
		$this->getType();
	}
	
	private function getType()
	{
		$this->type = 
			$this->type ?: 				// or
			$this->getTag("var") ?: 	// or
			$this->getTag("return") ?: 	// or
			null;
		
		if(strpos($this->type, "\\") !== false)
		{
			$elements = explode("\\", $this->type);
			$this->type = array_pop($elements);
		}
	}
	
	protected function getTag($tag)
	{
		if($tag[0] != "@")
		{	$tag = "@" . $tag;	}
		
		$docs = explode(PHP_EOL, $this->doc);
		
		foreach($docs as $doc)
		{
			if(($pos = strpos($doc, $tag)) !== false)
			{
				$elements = explode(" ", trim(substr($doc, $pos + strlen($tag))));
				return trim($elements[0]);
			}
		}
		
		return null;
	}
	
	protected function getGetter($name)
	{
		$name[0] = strtoupper($name[0]);
		return "get" . $name;
	}
	
	
	protected function getNamespace($classname)
	{
		$nameElements = explode("\\", $classname);
		
		$classname = array_pop($nameElements);
		$namespace = implode("\\", array_filter($nameElements));
		
		return array($namespace, $classname);
	}
}