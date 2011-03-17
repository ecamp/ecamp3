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

namespace Service\Searching;


class SearchUserService
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 * @Inject EntityManager
	 */
	protected $em;


		/*
		x: query
		y: user

		p(y|x) = p(x|y) * p(y)


		p(y e friend)
		p(y e sameGroup)
		p(y e unknown)


		p(x|y)
		*/



	public function search($query)
	{
		$query = trim($query);
		$queries = explode(" ", $query);


		$users = $this->em->getRepository('Entity\User')->findAll();

		/** @var \Entity\User $user */
		foreach($users as $user)
		{
			$this->getRating($queries, $user);
		}

	}


	/*
	 * Calculate p(x|y)
	 */

	private function getRating($queries, \Entity\User $user)
	{
		$username = $user->getUsername();

		foreach($queries as $q)
		{
			


		}




	}




	public function searchForUser($query)
	{
		$results = new \Doctrine\Common\Collections\ArrayCollection();


		$query = trim($query);
		$queries = explode(" ", $query);

		foreach($queries as $query)
		{
			$query = trim($query);

			$this->searchForUserByMailAddress($results, $query);

			$this->searchForUserByCamelCase($results, $query);
		}







		/*
				$qb = $this->em->createQueryBuilder();

				$qb
					->select('u')
					->from('Entity\User', 'u');

				foreach($query as $q)
				{
					$qb->orWhere($qb->expr()->like('u.username', $qb->expr()->literal($q."%")));
					$qb->orWhere($qb->expr()->like('u.email', $qb->expr()->literal($q."%")));

				}



				$searchResults = $qb->getQuery()->execute();

				foreach($searchResults as $result)
				{
					var_dump($result->getUsername());
				}

				//var_dump($searchResult);

		 */
		die();
	}



	private function searchForUserByMailAddress(
		\Doctrine\Common\Collections\ArrayCollection $results, $query)
	{
		$mailValidator = new \Zend_Validate_EmailAddress();

		if(!$mailValidator->isValid($query))
		{	return;	}

		/** @var $user \Entity\User */
		$user = $this->em->getRepository('Entity\User')->findOneBy(array('email' => $query));

		$this->addUserToResult($results, $user);

		
		$results[$user->getId()]->found('MailAddress', 1);
	}


	private function searchForUserByCamelCase(
		\Doctrine\Common\Collections\ArrayCollection $results, $query)
	{
		$alphaValidator = new \Zend_Validate_Alpha();

		if(!$alphaValidator->isValid($query))
		{	return;	}


		$query = preg_replace("([A-Z])", ' $0', $query);
		$query = trim($query);
		$qWords = explode(" ", $query);


		$qb = $this->em->createQueryBuilder();
		$qb->select('u')->from('Entity\User', 'u');

		foreach($qWords as $qWord)
		{
			$qb->orWhere($qb->expr()->like('u.username', $qb->expr()->literal($qWord."%")));
			$qb->orWhere($qb->expr()->like('u.scoutname', $qb->expr()->literal($qWord."%")));
			$qb->orWhere($qb->expr()->like('u.firstname', $qb->expr()->literal($qWord."%")));
			$qb->orWhere($qb->expr()->like('u.surname', $qb->expr()->literal($qWord."%")));
		}

		$users = $qb->getQuery()->execute();


		foreach($users as $user)
		{
			


		}

	}


	private function addUserToResult(
		\Doctrine\Common\Collections\ArrayCollection $results,
		\Entity\User $user)
	{
		if(!$results->containsKey($user->getId()))
		{
			$results[$user->getId()] = new UserSearchResult($user);
		}
	}


}
