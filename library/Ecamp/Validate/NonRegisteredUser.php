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

class NonRegisteredUser extends \Zend_Validate_Abstract
{
	const USER_IS_REGISTERED = 'UserIsRegistered';

	protected $_messageTemplates = array(
		self::USER_IS_REGISTERED => "'%value%' is already registered!"
	);


	/** @var \Doctrine\ORM\EntityManager */
	private $em;

	/** @var \Doctrine\ORM\EntityRepository */
	private $userRepository;


	private $field;
	


	public function __construct($field = null)
	{
		$this->field = $field;

		$this->em = \Zend_Registry::get('doctrine')->getEntityManager();
		$this->userRepository = $this->em->getRepository('Core\Entity\User');
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

		if($this->field == null)
		{
			/** @var $user \Entity\User */
			$user = $this->userRepository->find($value);
		}
		else
		{
			/** @var $user \Entity\User */
			$user = $this->userRepository->findOneBy(array($this->field => $value));
		}

		if(!is_null($user) && $user->getState() != \Entity\User::STATE_NONREGISTERED)
		{
			$this->_error(self::USER_IS_REGISTERED);
			return false;
		}

		return true;
	}
}
