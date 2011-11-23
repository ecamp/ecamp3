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

namespace CoreApi\Service;


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



		Select user.*, Max(user.prob) as maxProb

		From
		(
			(
			SELECT users . * , LENGTH(  'Fort' ) / LENGTH( users.username ) AS prob
			FROM users
			WHERE users.username LIKE  'Fort%'
			)

		 	UNION

		 	(
			SELECT users . * , LENGTH(  'Pi' ) / LENGTH( users.firstname ) AS prob
			FROM users
			WHERE users.firstName LIKE  'Pi%'
			)

			UNION

		 	(
			SELECT users . * , LENGTH(  'Pi' ) / LENGTH( users.surname ) AS prob
			FROM users
			WHERE users.surName LIKE  'Pi%'
			)
		
		) as user

		Group by user.id

	*/




	public function SearchForUser($query)
	{
		$q = $this->GetDqlSearchQuery($query);
		$users = $q->getResult();

		return $users;
	}


	/**
	 * @param  $query
	 * @return \Doctrine\ORM\Query
	 */
	private function GetDqlSearchQuery($query)
	{
		$query = trim($query);
		$queries = explode(" ", $query);

		$queryTableEntries = array();

		foreach($queries as $query)
		{
			$queryTableEntries[] = "SELECT '" . trim($query) . "' as query";
		}

		$queryTable = implode(" UNION ", $queryTableEntries);



		$sql  = "SELECT users.id, ";
		$sql .= "(";
		$sql .= "MAX(IF(users.username Like Concat(queryTable.query, '%'), length(queryTable.query)/length(users.username), 0)) +";
		$sql .= "MAX(IF(users.scoutname Like Concat(queryTable.query, '%'), length(queryTable.query)/length(users.scoutname), 0)) +";
		$sql .= "MAX(IF(users.firstname Like Concat(queryTable.query, '%'), length(queryTable.query)/length(users.firstname), 0)) +";
		$sql .= "MAX(IF(users.surname Like Concat(queryTable.query, '%'), length(queryTable.query)/length(users.surname), 0))";
		$sql .= ") as prob ";

		$sql .= "FROM ";
		$sql .= "(";
		$sql .= $queryTable;
		$sql .= ") as queryTable, ";
		$sql .= "users ";

		$sql .= "WHERE ";
		$sql .= "users.username Like Concat(queryTable.query, '%') OR ";
		$sql .= "users.scoutname Like Concat(queryTable.query, '%') OR ";
		$sql .= "users.firstname Like Concat(queryTable.query, '%') OR ";
		$sql .= "users.surname Like Concat(queryTable.query, '%') ";

		$sql .= "GROUP BY users.id ";
		$sql .= "ORDER BY prob DESC;";



		$rms = new \Doctrine\ORM\Query\ResultSetMapping();
		$rms->addScalarResult('id', 'id');
		$rms->addScalarResult('prob', 'prob');

		/** @var \Doctrine\ORM\NativeQuery $q */
		$sqlQuery = $this->em->createNativeQuery($sql, $rms);


		$sqlResults = $sqlQuery->getResult();
		$userIds = array();
		
		foreach($sqlResults as $sqlResult)
		{	$userIds[] = $sqlResult['id'];	}


		$dql  = "SELECT user FROM Entity\User user ";

		if(empty($userIds))
		{	$dql .= "WHERE 0=1";	}
		else
		{	$dql .= "WHERE user.id in (" . implode(", ", $userIds) . ")";	}


		/** @var \Doctrine\ORM\Query $dqlQuery */
		$dqlQuery = $this->em->createQuery($dql);

		return $dqlQuery;
	}


}
