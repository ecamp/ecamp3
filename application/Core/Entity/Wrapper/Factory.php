<?php

namespace Core\Entity\Wrapper;

class Factory
{
	
	public static function createFiles($entityClass)
	{
		if(substr($entityClass, 0, 1) != "\\")
		{	$entityClass = "\\" . $entityClass;	}
		
		list($namespace, $classname) = self::loadClass($entityClass);
		
		$entityWrapperBody  = "namespace CoreApi\Entity;";
		$entityWrapperBody .= "\n\n";
		$entityWrapperBody .= self::createClass($entityClass);
		
		$entityWrapperFile = new \Zend_CodeGenerator_Php_File();
		$entityWrapperFile->setBody($entityWrapperBody);
		
		
		$entityListWrapperBody  = "namespace CoreApi\Entity;";
		$entityListWrapperBody .= "\n\n";
		$entityListWrapperBody .= self::createListClass($entityClass);
		
		$entityListWrapperFile = new \Zend_CodeGenerator_Php_File();
		$entityListWrapperFile->setFilename($classname . "List");
		$entityListWrapperFile->setBody($entityListWrapperBody);
		
		
		$entityWrapperFile->setFilename(APPLICATION_PATH . "/CoreApi/Entity/" . $classname . ".php");
		$entityWrapperFile->write();
		
		$entityListWrapperFile->setFilename(APPLICATION_PATH . "/CoreApi/Entity/" . $classname . "List.php");
		$entityListWrapperFile->write();
	}
	
	
	public static function createClass($entityClass)
	{
		list($namespace, $classname) = self::loadClass($entityClass);
		
		$refClass = new \ReflectionClass($entityClass);
		
		$class = new \Zend_CodeGenerator_Php_Class();
		$class->setName($classname);
		$class->setExtendedClass('BaseEntity');
		
		$classD = new \Zend_CodeGenerator_Php_Docblock();
		$classD->setLongDescription("This class is autogenerated!" . PHP_EOL . "Do not edit this class!");
		$class->setDocblock($classD);
		
		
		$ar = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
		$ar->addNamespace("Core\Entity\Annotations");
		
		$methods = array();
		
		
		//  Create Constructor
		// ====================
		$c = new \Zend_CodeGenerator_Php_Method();
		
		$p = new \Zend_CodeGenerator_Php_Parameter();
		$p->setName("entity");
		$p->setType($entityClass);
		
		$c->setParameter($p);
		$c->setName("__construct");
		$c->setBody('$this->wrappedObject = $entity;');
		
		$methods[] = $c;
		
		
		//  Create Getter for public properties:
		// ======================================
		foreach($refClass->getProperties() as $property)
		{
			$pas = $ar->getPropertyAnnotations($property);
			
			foreach($pas as $pa)
			{	$methods[] = $pa->createAccessor($property);	}
		}
		
		
		//  Create Methots for public methods:
		// ====================================
		foreach($refClass->getMethods() as $method)
		{
			$mas = $ar->getMethodAnnotations($method);
			
			foreach($mas as $ma)
			{	$methods[] = $ma->createAccessor($method);	}
		}
		
		$class->setMethods($methods);
		
		
		
		return $class->generate();
	}
	
	
	public static function createListClass($entityClass)
	{
		list($namespace, $classname) = self::loadClass($entityClass);
		
		$class = new \Zend_CodeGenerator_Php_Class();
		$class->setName($classname . "List");
		$class->setImplementedInterfaces(array('\Iterator', '\ArrayAccess'));
		$class->setExtendedClass('BaseEntity');
		
		$classD = new \Zend_CodeGenerator_Php_Docblock();
		$classD->setLongDescription("This class is autogenerated!" . PHP_EOL . "Do not edit this class!");	
		$class->setDocblock($classD);
		
		
		$p = new \Zend_CodeGenerator_Php_Parameter();
		$p->setName("entityList");
		
		$c = new \Zend_CodeGenerator_Php_Method();
		$c->setName("__construct");
		$c->setBody(
'
if(is_array($entityList))
{
	$this->wrappedObject = $entityList;
	return;
}

if($entityList instanceof \Iterator)
{
	$this->wrappedObject = $entityList;
	return;
}

if($entityList instanceof \IteratorAggregate)
{
	$this->wrappedObject = $entityList->getIterator();
	return;
}

throw new \Exception("List is not an Array or any other valid type.");
');
		$c->setParameter($p);
		
		
		//  ArrayAccess Methods:
		// ======================
		
		$existP = new \Zend_CodeGenerator_Php_Parameter();
		$existP->setName("offset");
		
		$exist = new \Zend_CodeGenerator_Php_Method();
		$exist->setName("offsetExists");
		$exist->setParameter($existP);
		$exist->setBody('return array_key_exists($offset, $this->wrappedObject);');
		
		
		$getP = new \Zend_CodeGenerator_Php_Parameter();
		$getP->setName("offset");
		
		$getDRT = new \Zend_CodeGenerator_Php_Docblock_Tag_Return();
		$getDRT->setDatatype('\CoreApi\Entity\\' . $classname);
		
		$getD = new \Zend_CodeGenerator_Php_Docblock();
		$getD->setTag($getDRT);
		
		$get = new \Zend_CodeGenerator_Php_Method();
		$get->setName("offsetGet");
		$get->setParameter($getP);
		$get->setDocblock($getD);
		$get->setBody('return $this->offsetExists($offset) ? new \CoreApi\Entity\\' . $classname . '($this->wrappedObject[$offset]) : null;');
		
		
		$setP1 = new \Zend_CodeGenerator_Php_Parameter();
		$setP1->setName("offset");
		$setP2 = new \Zend_CodeGenerator_Php_Parameter();
		$setP2->setName("value");
		
		$set = new \Zend_CodeGenerator_Php_Method();
		$set->setName("offsetSet");
		$set->setParameters(array($setP1, $setP2));
		$set->setBody('throw new \Exception("This is a Readonly List");');
		
		
		$unsetP = new \Zend_CodeGenerator_Php_Parameter();
		$unsetP->setName("offset");
		
		$unset = new \Zend_CodeGenerator_Php_Method();
		$unset->setName("offsetUnset");
		$unset->setParameter($unsetP);
		$unset->setBody('throw new \Exception("This is a Readonly List");');
		
		
		
		//  Iterator Methods:
		// ===================
		
		$rewind = new \Zend_CodeGenerator_Php_Method();
		$rewind->setName("rewind");
		$rewind->setBody('reset($this->wrappedObject);');
		
		
		$currentDRT = new \Zend_CodeGenerator_Php_Docblock_Tag_Return();
		$currentDRT->setDatatype('\CoreApi\Entity\\' . $classname);
		
		$currentD = new \Zend_CodeGenerator_Php_Docblock();
		$currentD->setTag($currentDRT);
		
		$current = new \Zend_CodeGenerator_Php_Method();
		$current->setName("current");
		$current->setBody('return $this->valid() ? new \CoreApi\Entity\\' . $classname . '(current($this->wrappedObject)) : null;');
		$current->setDocblock($currentD);
		
		
		$key = new \Zend_CodeGenerator_Php_Method();
		$key->setName("key");
		$key->setBody('return key($this->wrappedObject);');
		
		$next = new \Zend_CodeGenerator_Php_Method();
		$next->setName("next");
		$next->setBody('next($this->wrappedObject);');
		
		$valid = new \Zend_CodeGenerator_Php_Method();
		$valid->setName("valid");
		$valid->setBody('return (current($this->wrappedObject) !== false);');
		
		$class->setMethods(array($c, $exist, $get, $set, $unset, $rewind, $current, $key, $next, $valid));
		
		
		return $class->generate();
	}
	
	private static function loadClass($entityClass)
	{
		if(!class_exists($entityClass))
		{
			\Zend_Loader_Autoloader::autoload($entityClass);
		}
		
		if(!class_exists($entityClass))
		{
			throw new Exception("Entity $entityClass can not be loaded");
		}
		
		$nameElements = explode("\\", $entityClass);
		
		$classname = array_pop($nameElements);
		$namespace = implode("\\", array_filter($nameElements));
		
		return array($namespace, $classname);
	}
	
}