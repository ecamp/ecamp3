<?php

namespace CoreApi\Service;

use Zend_Registry;
use Exception;
	
class UserService
{

	/**
	 * @var \Doctrine\ORM\EntityManager
	 * @Inject EntityManager
	 */
	protected $em;

	/**
     * @var \Doctrine\ORM\EntityRepository
	 * @Inject CampRepository
	 */
	private $campRepo;
	
	/**
     * @var \Repository\UserRepository
     * @Inject UserRepository
     */
    private $userRepo;

	/**
     * @var \Doctrine\ORM\EntityRepository
     * @Inject UserCampRepository
     */
    private $userCampRepo;


	public function init()
	{}


    public function __construct()
    {}

	public function getAllUsers()
	{
		return $this->userRepo->findAll();
	}

	public function createLogin(\Core\Entity\User $user, $password)
	{
		$login = new \Core\Entity\Login();
		$login->setNewPassword($password);

		$login->setUser($user);

		return $login;
	}

	
	public function registerUser($params)
	{
		$email = $params['email'];
		
		$this->em->getConnection()->beginTransaction();
		
		try 
		{
			$user = $this->userRepo->findOneBy(array('email' => $email));
			
			if(is_null($user))
			{
				$user = new \Core\Entity\User();
				$user->setEmail($email);
				
				$this->em->persist($user);
			}
			
			if($user->getState() != \Core\Entity\User::STATE_NONREGISTERED)
			{
				throw new Exception("This eMail-Adress is already registered!");
			}
			
			$user->setUsername($params['username']);
			$user->setScoutname($params['scoutname']);
			$user->setFirstname($params['firstname']);
			$user->setSurname($params['surname']);
			$user->setState(\Core\Entity\User::STATE_REGISTERED);
			
			$login = $this->createLogin($user, $params['password1']);
			
			$this->em->persist($login);
			$this->em->flush();
			$this->em->getConnection()->commit();
			
			return $user;
		}
		catch (Exception $e)
		{
			$this->em->getConnection()->rollback();
			
			throw $e;
		}
	}
	
	
	public function activateUser($userId, $key)
	{
		/** @var $user \Entity\User */
		$user = $this->userRepo->find($userId);

		if(is_null($user))
		{	return false;	}

		if($user->getState() != \Core\Entity\User::STATE_REGISTERED)
		{	return false;	}

		return $user->activateUser($key);
	}

	public function addUserToCamp($user,$camp)
	{
		/* besser: via Model lšsen, z.B. user->doIBelongToCamp */
		$res = $this->userCampRepo->findBy(array('user' => $user->getId(), 'camp' => $camp->getId() ));

		if( $res == null )
		{
			$userCamp = new \Core\Entity\UserCamp();
			$userCamp->setUser($user);
			$userCamp->setCamp($camp);

			$this->em->persist($userCamp);
			$this->em->flush();
		}
	}

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
