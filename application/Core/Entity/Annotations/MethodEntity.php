<?php

namespace Core\Entity\Annotations;

class MethodEntity extends Annotation
{
	
	private static $body = 'return new \CoreApi\Entity\{CLASSNAME}($this->entity->{METHOD}({PARAMS});';
	private static $search = array("{CLASSNAME}", "{METHOD}", "{PARAMS}");
		
	
	public function createAccessor(\ReflectionMethod $rm)
	{
		$this->setDoc($rm->getDocComment());
		
		$m = new \Zend_CodeGenerator_Php_Method();
		$m->setName($rm->getName());
		
		
		$zrm = new \Zend_Reflection_Method($rm->class, $rm->name);
		$params = array();
		
		foreach($zrm->getParameters() as $zrp)
		{
			$p = \Zend_CodeGenerator_Php_Parameter::fromReflection($zrp);
			$m->setParameter($p);
				
			$params[$p->getPosition()] = "$" . $p->getName();
		}
		
		$replace = array($this->type, $rm->getName(), implode(", ", $params));
		$m->setBody(str_replace(self::$search, $replace, self::$body));
		
		
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