<?php

namespace CoreApi\Service;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;

/**
 * @method CoreApi\Service\FriendService Simulate
 */
class FriendService
	extends ServiceBase
{
	
	/**
	 * @var CoreApi\Service\UserService
	 * @Inject Core\Service\UserService
	 */
	private $userService;
	
	
	/**
	 * @var Core\Repository\UserRepository
	 * @Inject Core\Repository\UserRepository
	 */
	protected $userRepo;
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Get');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'getOpenRequest');	
	}
	
	
	/**
	 * Returns a List containing the Freind of $user
	 * If no User defined, the authentificated User is taken.
	 * 
	 * @param \CoreApi\Entity\User|int|string $user
	 *
	 * @return array
	 */
	public function Get($user = null)
	{
		$user = $this->userService->Get($user);
		
		return $this->userRepo->findFriendsOf($user);
	}
	
	
	/**
	 * Returns a list containing the open 
	 * requests of the authentificated User.
	 * 
	 * @return array
	 */
	public function getOpenRequest()
	{
		$user = $this->userService->Get();
		
		return $this->userRepo->findFriendshipInvitationsOf($user);
	}
	
	
	/**
	 * Returns a list containing the open 
	 * invitations of the authentificated User.
	 *
	 * @return array
	 */
	public function getOpenInvitations()
	{
		$user = $this->userService->get();
		
		// TODO: Implement findOpenFreindshipInvitations!!
		// return $this->userRepo->findOpenFreindshipInvitations($user);
		
		return array();
	}
	
	
	/**
	 * The authentificated User requests a Freindship to $toUser
	 * 
	 * @param \CoreApi\Entity\User|int|string $toUser
	 * 
	 * @throws \Exception
	 * 
	 * @return \CoreApi\Entity\UserRelationship
	 */
	public function request($toUser)
	{
		$user 	= $this->userService->get();
		$toUser = $this->userService->get($toUser);
		
		
		if($toUser->getId() == $user->getId())
		{	throw new \Exception("You tried to request Friendship to your self!");	}
		
		//TODO: Check, if Friendship or Request allready exist!
		
		$rel = new \CoreApi\Entity\UserRelationship($user, $toUser);
		
		$user->getRelationshipTo()->add($rel);
		$toUser->getRelationshipFrom()->add($rel);
		
		return $rel;
	}
	
	
	/**
	 * The authentificated User accepts a 
	 * Freindship request from $fromUser
	 * 
	 * @param \CoreApi\Entity\User|int|string $fromUser
	 * 
	 * @throws \Exception
	 * 
	 * @return \CoreApi\Entity\UserRelationshp
	 */
	public function accept($fromUser)
	{
		$user 		= $this->userService->get();
		$fromUser 	= $this->userService->get($fromUser);

		if($user->getId() == $fromUser->getId())
		{	throw new \Exception("You tried to accept Freindship Request from yourself!");	}
		
		// TODO: Check, if Friendship Request is available!
		
		$rel = new \CoreApi\Entity\UserRelationship($user, $fromUser);
		
		$user->getRelationshipTo()->add($rel);
		$fromUser->getRelationshipFrom()-add($rel);
		
		return $rel;
	}
	
	
	/**
	 * The authentificated User rejects a 
	 * Friendship request from $fromUser.
	 * 
	 * @param \CoreApi\Entity\User|int|string $fromUser
	 * 
	 * @throws \Exception
	 */
	public function reject($fromUser)
	{
		$user 		= $this->userService->get();
		$fromUser 	= $this->userService->get($fromUser);
		
		if($user->getId() == $fromUser->getId())
		{	throw new \Exception("You tried to reject Freindship Request from yourself!");	}
		
		// TODO: Find Friendship Request!
		// $rel = $this->userRelationshipRepo->get($fromUser, $user);
		$rel = null;
		
		$idx = $fromUser->getRelationshipTo()->indexOf($rel);
		$fromUser->getRelationshipTo()->remove($idx);
		
		$idx = $user->getRelationshipFrom()->indexOf($rel);
		$user->getRelationshipFrom()->remove(idx);
		
		$this->em->remove($rel);
	}
	
	
	/**
	 * The authentificated User terminates 
	 * a Freindship to $toUser
	 * 
	 * @param \CoreApi\Entity\User|int|string $toUser
	 * 
	 * @throws \Exception
	 */
	public function terminate($toUser)
	{
		$user 	= $this->userService->get();
		$toUser = $this->userService->get($toUser);
		
		if($user->getId() == $toUser->getId())
		{	throw new \Exception("You tried to terminate Freindship to yourself!");	}
		
		
		
		//  Remove first Freindship Entity:
		// =================================
		
		// TODO: Find Friendship Request!
		// $rel = $this->friendService->get($toUser, $User);
		$rel = null;
		
		$idx = $toUser->getRelationshipTo()->indexOf($rel);
		$toUser->getRelationshipTo()->remove($idx);
		
		$idx = $user->getRelationshipFrom()->indexOf($rel);
		$user->getRelationshipFrom()->remove(idx);
		
		$this->em->remove($rel);
		
		
		//  Remove second Freindship Entity:
		// ==================================
		
		// TODO: Find Friendship Request!
		// $rel = $this->friendService->get($user, $toUser);
		$rel = null;
		
		$idx = $user->getRelationshipTo()->indexOf($rel);
		$user->getRelationshipTo()->remove($idx);
		
		$idx = $toUser->getRelationshipFrom()->indexOf($rel);
		$toUser->getRelationshipFrom()->remove(idx);
		
		$this->em->remove($rel);
	}
	
	
	
}