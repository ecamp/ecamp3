<?php
/**
 *
 * Copyright (C) 2011 pirminmattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Ecamp\Validate;

class NoRecordExist extends \Zend_Validate_Abstract
{
	const RECORD_EXIST = 'RecordExist';

	protected $_messageTemplates = array(
		self::RECORD_EXIST => "'%value%' is no more available!"
	);


	private $entityClass;

	private $field;

	private $criteria;

	/** @var \Doctrine\ORM\EntityManager */
	private $em;

	/** @var \Doctrine\ORM\EntityRepository */
	private $entityRepository;


	


	public function __construct($entityClass, $field = null, $criteria = null)
	{
		$this->entityClass = $entityClass;
		$this->field = $field;
		$this->criteria = $criteria;

		$this->em = \Zend_Registry::get('doctrine')->getEntityManager();
		$this->entityRepository = $this->em->getRepository($entityClass);
	}


	/**
	 * Returns true if and only if $value meets the validation requirements
	 *
	 * If $value fails validation, then this method returns false, and
	 * getMessages() will return an array of messages that explain why the
	 * validation failed.
	 *
	 * @param  mixed $value
	 * @return boolean
	 * @throws Zend_Validate_Exception If validation of $value is impossible
	 */
	public function isValid($value)
	{
		$this->_setValue($value);

		if($this->field == null && $this->criteria == null)
		{
			$record = $this->entityRepository->find($value);
		}
		else
		{
			$criteria = $this->criteria ? $this->criteria : array();
			$criteria[$this->field] = $value;
			
			$record = $this->entityRepository->findOneBy($criteria);
		}

		if(!is_null($record))
		{
			$this->_error(self::RECORD_EXIST);
			return false;
		}

		return true;
	}
}
