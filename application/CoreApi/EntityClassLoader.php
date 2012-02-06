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
		
		if(file_exists($coreApiFilePath))
		{	require($coreApiFilePath);	return true;	}
		
		
		
		// Try to create the required CoreApi Entity File!!
		
		$coreFilePathA =	str_replace('CoreApi', 'Core', $coreApiFilePath);
		$coreFilePathB =	str_replace('List.php', '.php', $coreFilePathA);
		
		$coreClassNameA = str_replace('CoreApi', 'Core', $classname);
		$coreClassNameB = str_replace('List', '', $coreClassNameA);
		
		
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
		\Zend_Loader_Autoloader::autoload("Core\Entity\Annotations\Property");
		\Zend_Loader_Autoloader::autoload("Core\Entity\Annotations\PropertyEntity");
		\Zend_Loader_Autoloader::autoload("Core\Entity\Annotations\PropertyEntityList");
		\Zend_Loader_Autoloader::autoload("Core\Entity\Annotations\Method");
		\Zend_Loader_Autoloader::autoload("Core\Entity\Annotations\MethodEntity");
		\Zend_Loader_Autoloader::autoload("Core\Entity\Annotations\MethodEntityList");
	}
	
	
}