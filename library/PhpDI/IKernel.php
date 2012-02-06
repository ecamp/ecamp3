<?php

namespace PhpDI;


interface IKernel
{
	public function Get($classname);
	
	public function Inject($object);
	
	public function Bind($classname);
	
	public function Rebind($classname);
	
	public function Unbind($classname);
}