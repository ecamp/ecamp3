<?php

namespace CoreApi\Service;

class Session extends ServiceAbstract
{
	
	/**
	 * Returns the Session Namespace
	 * 
	 * @param string $namespace
	 * 
	 * @return \Zend_Session_Namespace
	 */
	public function get($namespace = 'Default')
	{
		return new \Zend_Session_Namespace($namespace);
	}
	
	
	/**
	 * Creates a new Namespace in the Session
	 * 
	 * @param string $namespace
	 * 
	 * @return \Zend_Session_Namespace
	 */
	public function create($namespace)
	{
// 		if(\Zend_Session::namespaceIsset($namespace))
// 		{
			\Zend_Session::namespaceUnset($namespace);
// 		}
		
		return new \Zend_Session_Namespace($namespace);
	}
	
	
	/**
	 * Deletes a Namespace in the Session
	 *
	 * @param string $namespace
	 */
	public function delete($namespace)
	{
		\Zend_Session::namespaceUnset($namespace);
	}
	
}