<?php

namespace CoreApi\Service;

use Zend_Registry;
use Exception;
	
class UserService
{

	/** returns all ur (true) friends */
//	public function getFriendsOf($user)
//	{
//		$query = $this->em->getRepository("Entity\User")->createQueryBuilder("u")
//				->innerJoin("u.relationshipFrom","rel_to")
//				->innerJoin("rel_to.to", "friend")
//				->innerJoin("friend.relationshipFrom", "rel_back")
//				->where("rel_to.type = ".\Entity\UserRelationship::TYPE_FRIEND)
//				->andwhere("rel_back.type = ".\Entity\UserRelationship::TYPE_FRIEND)
//				->andwhere("rel_back.to = u.id")
//				->andwhere("friend.id = ".$user->getId())
//				->getQuery();
//
//	    return $query->getResult();
//	}

	/** returns all users that wants u as friend, but which have not accepted yet */
//	public function getFriendshipInvitationsOf($user)
//	{
//		$query = $this->em->getRepository("Entity\User")->createQueryBuilder("u")
//				->innerJoin("u.relationshipFrom","rel_to")
//				->innerJoin("rel_to.to", "friend")
//				->leftJoin("friend.relationshipFrom", "rel_back", \Doctrine\ORM\Query\Expr\Join::WITH, 'rel_back.to = rel_to.from' )
//				->where("rel_to.type = ".(\Entity\UserRelationship::TYPE_FRIEND))
//				->andwhere("rel_back.to IS NULL")
//				->andwhere("friend.id = ".$user->getId())
//				->getQuery();
//				
//	    return $query->getResult();
//	}

//	public function getMembershipRequests($user){
//		$query = $this->em->getRepository("Entity\UserGroup")->createQueryBuilder("ug")
//					->innerJoin("ug.group", "g")
//					->innerJoin("g.userGroups", "manager")
//					->where("manager.user = ".$user->getId())
//					->andwhere("manager.role = ".(\Entity\UserGroup::ROLE_MANAGER))
//					->andwhere("ug.requestedRole = 10")
//					->getQuery();
//					
//		return $query->getResult();
//	}

//	public function getMembershipInvitations(\Entity\User $user)
//	{
//		$query = $this->em->getRepository("Entity\UserGroup")->createQueryBuilder("ug")
//					->where("ug.user = " . $user->getId())
//					->andWhere("ug.invitationAccepted = FALSE")
//					->getQuery();
//		
//		return $query->getResult();
//	}

}
