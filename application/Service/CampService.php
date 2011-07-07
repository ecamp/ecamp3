<?php

namespace Service;

class CampService
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
     * @Inject \Repository\UserRepository
     */
    private $userRepo;

	/**
     * @var \Doctrine\ORM\EntityRepository
     * @Inject UserCampRepository
     */
    private $userCampRepo;
	
	
    /**
     * Creates a new Camp
     * @param \Entity\Group $group Owner of the new Camp
     * @param \Entity\User $user Creator of the new Camp
     * @param Array $params
     * @return Boolean Whether creation was successful
     * @throws Exception
     */
    public function CreateCampForGroup(\Entity\Group $group, \Entity\User $creator, $params)
    {
    	$this->em->getConnection()->beginTransaction();
		try
		{
			$camp = $this->CreateCamp($creator, $params);
			$period = $this->CreatePeriod($camp, $params);
			
			$camp->setGroup($group);

			
			$this->em->persist($camp);
			$this->em->persist($period);
			
			$this->em->flush();
			$this->em->getConnection()->commit();
			
			return $camp;
		}
		catch (Exception $e)
		{
			$this->em->getConnection()->rollback();
			$this->em->close();

			throw $e;
		}
    }
    
    
	/**
     * Creates a new Camp
     * @param \Entity\User $user Creator of the new Camp
     * @param Array $params
     * @return Boolean Whether creation was successful
     * @throws Exception
     */
    public function CreateCampForUser(\Entity\User $creator, $params)
    {
		$this->em->getConnection()->beginTransaction();
		try
		{
			$camp = $this->CreateCamp($creator, $params);
			$period = $this->CreatePeriod($camp, $params);
			
			$camp->setOwner($creator);
			
			$this->CreateDefaultAclRoles($camp);
			
			$this->em->persist($camp);
			$this->em->persist($period);

			$this->em->flush();
			$this->em->getConnection()->commit();
			
			return $camp;
		}
		catch (\Exception $e)
		{
			$this->em->getConnection()->rollback();
			$this->em->close();
			
			die($e->getMessage());
			exit;
			
			throw $e;
		}
		
    }
    
    
    
    
    private function CreateCamp(\Entity\User $creator, $params)
    {
    	$camp = new \Entity\Camp();
    	
    	$camp->setCreator($creator);
    	
    	if(isset($params['name']))
    	{	$camp->setName($params['name']);	}
    	
    	if(isset($params['title']))
    	{	$camp->setTitle($params['title']);	}
    	
    	return $camp;
    }
    
    private function CreatePeriod(\Entity\Camp $camp, $params)
    {
    	$period = new \Entity\Period($camp);
	
		$from = new \DateTime($params['from'], new \DateTimeZone("GMT"));
		$to   = new \DateTime($params['to'], new \DateTimeZone("GMT"));
		
		$period->setStart($from);
		$period->setDuration(($to->getTimestamp() - $from->getTimestamp())/(24 * 60 * 60) + 1);
		
		return $period;
    }
    
    
    private function CreateRole(\Entity\Camp $camp, $roleName, $roleDescription)
    {
    	$role = new \Entity\AclRole($camp);
    	$role->setName($roleName);
    	$role->setDescription($roleDescription);
    	
    	$camp->getAclRoles()->add($role);
    	
    	return $role;
    }
    
    private function CreateRule(\Entity\AclRole $role, $type, $resource, $privilege = null)
    {
    	$rule = new \Entity\AclRule($role);
    	
    	$rule->setType($type);
    	$rule->setResource($resource);
    	$rule->setPrivileg($privilege);
    	
    	$role->getAclRules()->add($rule);
    	
    	return $rule;
    }
    
    private function CreateDefaultAclRoles(\Entity\Camp $camp)
    {
    	$config = new \Zend_Config_Ini(APPLICATION_PATH . '/configs/campAcl.ini', APPLICATION_ENV);
    	
    	$defaultRoles = $config->get("DefaultRoles");
    	
		foreach($defaultRoles as $key => $role)
		{
			$roleName = $role->get('Name', $key);
			$roleDescription = $role->get('Description', '');
			
			$role = $this->CreateRole($camp, $roleName, $roleDescription);
			
			$this->CreateDefaultAclRules($role, $key);
		}
    }
    
    private function CreateDefaultAclRules(\Entity\AclRole $role, $key)
    {
    	$config = new \Zend_Config_Ini(APPLICATION_PATH . '/configs/campAcl.ini', APPLICATION_ENV);
    	$defaultRules = $config->get('DefaultRules')->get($key);
    	
    	foreach($defaultRules as $resource => $value)
    	{
    		if(is_string($value))
    		{	$this->CreateRule($role, $value, $resource);	}
    		else 
    		{
    			foreach($value as $privilege => $type)
    			{	$this->CreateRule($role, $type, $resource, $privilege);	}
    		}
    		
    	}
    }
}
