<?php

namespace CoreApi;

class EntityClassLoader implements \Zend_Loader_Autoloader_Interface
{
	private $fileExtension = '.php';
	private $namespaceSeparator = '\\';
	
	
	public function autoload($classname)
	{
		$coreApiFilePath = 
			APPLICATION_PATH . DIRECTORY_SEPARATOR
			. str_replace($this->namespaceSeparator, DIRECTORY_SEPARATOR, $classname)
			. $this->fileExtension;
		
		
		// Try to create the required CoreApi Entity File!!
		
		$coreFilePathA =	str_replace('CoreApi', 'Core', $coreApiFilePath);
		$coreFilePathB =	str_replace('List.php', '.php', $coreFilePathA);
		
		$coreClassNameA = str_replace('CoreApi', 'Core', $classname);
		$coreClassNameB = str_replace('List', '', $coreClassNameA);
		
		
		if(file_exists($coreApiFilePath))
		{
			
			if(file_exists($coreFilePathA))
			{
				if(filemtime($coreApiFilePath) < filemtime($coreFilePathA))
				{
					$this->loadAnnotations();
					\Core\Entity\Wrapper\Factory::createFiles($coreClassNameA);
				}
			}
			
			if(file_exists($coreFilePathB))
			{
				if(filemtime($coreApiFilePath) < filemtime($coreFilePathB))
				{
					$this->loadAnnotations();
					\Core\Entity\Wrapper\Factory::createFiles($coreClassNameB);
				}
			}
			
			require($coreApiFilePath);	return true;
		}
		
		
		
		if(file_exists($coreFilePathA))
		{
			$this->loadAnnotations();
			\Core\Entity\Wrapper\Factory::createFiles($coreClassNameA);
			
			require($coreApiFilePath);
			return true;
		}
		
		if(file_exists($coreFilePathB))
		{
			$this->loadAnnotations();
			\Core\Entity\Wrapper\Factory::createFiles($coreClassNameB);
				
			require($coreApiFilePath);
			return true;
		}
		
		return false;
	}
	
	private function loadAnnotations()
	{
		class_exists("Core\Entity\Annotations\Property");
		class_exists("Core\Entity\Annotations\PropertyEntity");
		class_exists("Core\Entity\Annotations\PropertyEntityList");
		class_exists("Core\Entity\Annotations\Method");
		class_exists("Core\Entity\Annotations\MethodEntity");
		class_exists("Core\Entity\Annotations\MethodEntityList");
	}
	
	
}