<?php

namespace Core\Service;

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
     * @Inject UserRepository
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
    public function CreateCampForGroup(\Core\Entity\Group $group, \Core\Entity\User $creator, $params)
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
     * @param \Entity\Group $group Owner of the new Camp
     * @param \Entity\User $user Creator of the new Camp
     * @param Array $params
     * @return Boolean Whether creation was successful
     * @throws Exception
     */
    public function CreateCampForUser(\Core\Entity\User $creator, $params)
    {
		$this->em->getConnection()->beginTransaction();
		try
		{
			$camp = $this->CreateCamp($creator, $params);
			$period = $this->CreatePeriod($camp, $params);
			
			$camp->setOwner($creator);
			
			
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
    
    
    private function CreateCamp(\Core\Entity\User $creator, $params)
    {
    	$camp = new \Core\Entity\Camp();
    	
    	$camp->setCreator($creator);
    	
    	if(isset($params['name']))
    	{	$camp->setName($params['name']);	}
    	
    	if(isset($params['title']))
    	{	$camp->setTitle($params['title']);	}
    	
    	return $camp;
    }
    
    private function CreatePeriod(\Core\Entity\Camp $camp, $params)
    {
    	$period = new \Core\Entity\Period($camp);
	
		$from = new \DateTime($params['from'], new \DateTimeZone("GMT"));
		$to   = new \DateTime($params['to'], new \DateTimeZone("GMT"));
		
		$period->setStart($from);
		$period->setDuration(($to->getTimestamp() - $from->getTimestamp())/(24 * 60 * 60) + 1);
		
		return $period;
    }
    
}
