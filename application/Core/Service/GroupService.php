<?php

namespace Core\Service;
use Zend_Registry;
use Exception;
	
class GroupService
{

	/**
	 * @var \Doctrine\ORM\EntityManager
	 * @Inject EntityManager
	 */
	private $em;
	
	/**
     * @var \Doctrine\ORM\EntityRepository
     * @Inject GroupRepository
     */
    private $groupRepo;

    
    
    
	public function getMembershipRequests(\Entity\Group $group)
	{
		$query = $this->em->getRepository("Entity\UserGroup")->createQueryBuilder("ug")
					->where("ug.group = " . $group->getId())
					->andwhere("ug.requestAcceptedBy IS NULL")
					->getQuery();
					
		return $query->getResult();
	}
	
	
	/**
	 * A $manager of the $group invites $user to be a memeber of the $group
	 * @param \Entity\User $user
	 * @param \Entity\Group $group
	 * @param \Entity\User $manager
	 */
	public function inviteUserToGroup(\Entity\User $user, \Entity\Group $group, \Entity\User $manager)
	{
		if(!$group->isManager($manager))
		{
			throw new Exception("User [".$manager->getUsername()."] is not allowed to invite other users to be members of the group [".$group->getName()."]");
		}
		
		$usergroup = new \Entity\UserGroup($user, $group);
		$usergroup->setRequestedRole(\Entity\UserGroup::ROLE_MEMBER);
		
		$usergroup->acceptRequest($manager);
		
		$user->getUsergroups()->add($usergroup);
		$group->getUserGroups()->add($usergroup);
		
		return $usergroup;
	}
	
	
	/**
	 * A invited user accepts its membership of the group
	 * @param \Entity\User $user
	 * @param \Entity\UserGroup $usergroup
	 * @throws Exception
	 */
	public function acceptMembershipInvitation(\Entity\User $user, \Entity\UserGroup $usergroup)
	{
		if(!$usergroup->isOpenInvitation())
		{
			throw new Exception("There is no Invitation to be accepted");
		}
		
		// Is it the right user, who accepts the invitation?
		if($usergroup->getUser() != $user)
		{
			throw new Exception("Invalid membership invitation acceptation");
		}
		
		$usergroup->acceptInvitation();
	}
	
	
	/**
	 * A invited user refuses its membership of a group
	 * @param \Entity\User $user
	 * @param \Entity\UserGroup $usergroup
	 * @throws Exception
	 */
	public function refuseMembershipInvitation(\Entity\User $user, \Entity\UserGroup $usergroup)
	{
		if(!$usergroup->isOpenInvitation())
		{
			throw new Exception("There is no Invitation to be refused");
		}
		
		// Is it the right user, who refuses the invitation?
		if($usergroup->getUser() != $user)
		{
			throw new Exception("Invalid membership invitation refuse");
		}
		
		$usergroup->getUser()->getUserGroups()->removeElement($usergroup);
		$usergroup->getGroup()->getUserGroups()->removeElement($usergroup);
		
		$this->em->remove($usergroup);
	}
	
	
	/**
	 * A admin of a group accepts a membership request
	 * @param \Entity\User $manager
	 * @param \Entity\UserGroup $usergroup
	 * @throws Exception
	 */
	public function acceptMembershipRequest(\Entity\User $manager, \Entity\UserGroup $usergroup)
	{
		if(!$usergroup->isOpenRequest())
		{
			throw new Exception("There is no Request to be accepted");
		}
		
		// Is $manager allowed to accept Request?
		if(!$usergroup->getGroup()->isManager($manager))
		{
			throw new Exception("User [" . $manager->getUsername() . "] is not allowed to accept this MembershipRequest");
		}
		
		$usergroup->acceptRequest($manager);
	}
	
	
	/**
	 * A manager of a group refuses a membership request
	 * @param \Entity\User $manager
	 * @param \Entity\UserGroup $usergroup
	 * @throws Exception
	 */
	public function refuseMembershipRequest(\Entity\User $manager, \Entity\UserGroup $usergroup)
	{
		if(!$usergroup->isOpenRequest())
		{
			throw new Exception("There is no Request to be refused");
		}
		
		// Is $manager allowed to refuse Request?
		if(!$usergroup->getGroup()->isManager($manager))
		{
			throw new Exception("User [" . $manager->getUsername() . "] is not allowed to refuse this MembershipRequest");
		}
		
		$usergroup->getUser()->getUserGroups()->removeElement($usergroup);
		$usergroup->getGroup()->getUserGroups()->removeElement($usergroup);
		
		$this->em->remove($usergroup);
	}
    
}
