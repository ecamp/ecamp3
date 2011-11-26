<?php

namespace Core\Entity\Annotations;

class PropertyEntity extends Annotation
{

	private static $body = 'return new \CoreApi\Entity\{CLASSNAME}($this->wrappedObject->{PROPERTY});';
	private static $search = array("{CLASSNAME}", "{PROPERTY}");

	
	public function createAccessor(\ReflectionProperty $rp)
	{
		$this->setDoc($rp->getDocComment());
		
		$m = new \Zend_CodeGenerator_Php_Method();
		$m->setName($this->getGetter($rp->getName()));


		$body = str_replace(self::$search, array($this->type, $rp->getName()), self::$body);
		$m->setBody($body);

		$d = new \Zend_CodeGenerator_Php_Docblock();
		$tags = array();


		if($this->type)
		{
			$rd = new \Zend_CodeGenerator_Php_Docblock_Tag_Return();
			$rd->setDatatype("\CoreApi\Entity\\" . $this->type);
			$tags[] = $rd;
		}

		$d->setTags($tags);
		$m->setDocblock($d);

		return $m;
	}

}