<?php

namespace CoreApi\Service;

class Group extends ServiceAbstract
{

	/**
	 * @var \Doctrine\ORM\EntityManager
	 * @Inject EntityManager
	 */
	protected $em;
    
    /**
	 * @var \CoreApi\Service\Camp
     * @Inject \CoreApi\Service\Camp
	 */
	private $campService;
	

	
    // public function index(){}
	public function get(){}
	public function update(){}
	public function delete(){}
    
    /**
     * Creates a new Camp
     * This method is protected, means it is only available from outside (magic!) if ACL is set properly
     * 
     * @param \Entity\Group $group Owner of the new Camp
     * @param \Entity\User $user Creator of the new Camp
     * @param Array $params
     * @return Camp object, if creation was successfull
     * @throws \Ecamp\ValidationException
     */
    protected function createCamp(\Core\Entity\Group $group, \Core\Entity\User $creator, $params)
    {
    	$this->em->getConnection()->beginTransaction();
		try
		{
			$camp = $this->campService->create($creator, $params);
			
			$camp->setGroup($group);

			$this->em->persist($camp);
			$this->em->flush();

			$this->em->getConnection()->commit();
			
			return $camp;
		}
		catch (\PDOException $e)
		{
			$this->em->getConnection()->rollback();
			$this->em->close();

			$form = new \Core\Form\Camp\Create();
			$form->getElement('name')->addError("Name has already been taken.");
			
			throw new \Ecamp\ValidationException($form);
		}
    }
    
	/**
	 * Setup ACL. Is used for manual calls of 'checkACL' and for automatic checking 
     * @see    CoreApi\Service\ServiceAbstract::_setupAcl()
     * @return void
     */
    protected function _setupAcl()
    {
        $this->_acl->allow('group_manager', $this, 'createCamp');
    }
}
