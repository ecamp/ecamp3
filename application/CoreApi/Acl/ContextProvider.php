<?php

namespace CoreApi\Acl;


class ContextProvider
{
	
	/**
	 * @var Core\Acl\ContextStorage
	 * @Inject Core\Acl\ContextStorage
	 */
	protected $contextStorage;
	
	
	public function getContext()
	{
		return $this->contextStorage->getReadonlyContext();
	}
	
}