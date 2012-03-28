<?php

namespace CoreApi\Acl;


class ContextProvider
{
	
	/**
	 * @var Core\Acl\ContextStorage
	 * @Inject Core\Acl\ContextStorage
	 */
	protected $contextStorage;
	
	
	/**
	 * @return CoreApi\Acl\Context
	 */
	public function getContext()
	{
		return $this->contextStorage->getContext();
	}
	
}