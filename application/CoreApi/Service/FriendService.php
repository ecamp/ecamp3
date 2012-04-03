<?php

namespace CoreApi\Service;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;
use CoreApi\Entity\UserRelationship;

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
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'getOpenInvitations');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'request');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'terminate');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'accept');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'reject');
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
	 * invitations other users have sent to me
	 *
	 * @return array
	 */
	public function getOpenInvitations()
	{
		$user = $this->userService->get();
		
		return $this->userRepo->findFriendshipInvitationsOf($user);
	}
	
	
	/**
	 * The authentificated User requests a Friendship to $toUser
	 * 
	 * @param \CoreApi\Entity\User|int|string $toUser
	 * 
	 * @throws \Exception
	 * 
	 * @return \CoreApi\Entity\UserRelationship
	 */
	public function request($toUser)
	{
		$user 	= $this->userService->Get();
		$toUser = $this->userService->Get($toUser);
		
		if($toUser == $user)
		{	throw new \Exception("You tried to request Friendship to your self!");	}
		
		if( $user->canIAdd($toUser) )
		{
			$rel = new UserRelationship($user, $toUser);
			$user->getRelationshipFrom()->add($rel);
			$toUser->getRelationshipTo()->add($rel);
			
			return true;
		}
		
		return false;
	}
	
	
	/**
	 * The authentificated User accepts a 
	 * Friendship request from $fromUser
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

		if($user == $fromUser)
		{	throw new \Exception("You tried to accept Friendship Request from yourself!");	}
		
		if( $user->receivedFriendshipRequestFrom($fromUser) )
		{
			$rel = new UserRelationship($user, $fromUser);
			$user->getRelationshipFrom()->add($rel);
			$fromUser->getRelationshipTo()->add($rel);
			
			return true;
		}
		
		return false;
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
		
		if($user == $fromUser)
		{	throw new \Exception("You tried to reject Friendship Request from yourself!");	}
		
		if( $user->receivedFriendshipRequestFrom($fromUser) )
		{
			$rel = $user->getRelFrom($fromUser);
			$user->getRelationshipTo()->removeElement($rel);
			$fromUser->getRelationshipFrom()->removeElement($rel);
			
			return true;
		}
		
		return false;
	}
	
	
	/**
	 * The authentificated User terminates 
	 * a Friendship to $toUser
	 * 
	 * @param \CoreApi\Entity\User|int|string $toUser
	 * 
	 * @throws \Exception
	 */
	public function terminate($toUser)
	{
		$user 	= $this->userService->get();
		$toUser = $this->userService->get($toUser);
		
		if($user == $toUser)
		{	throw new \Exception("You tried to terminate Friendship to yourself!");	}
		
		if( $user->isFriendOf($toUser) ){
			$rel = $user->getRelFrom($toUser);
			$user->getRelationshipTo()->removeElement($rel);
			$toUser->getRelationshipFrom()->removeElement($rel);
		
			$rel = $user->getRelTo($toUser);
			$user->getRelationshipFrom()->removeElement($rel);
			$toUser->getRelationshipTo()->removeElement($rel);
			
			return true;
		}
		
		return false;
	}
	
	
	
}