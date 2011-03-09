<?php

namespace Service;
use Zend_Registry;
	
class UserService
{

	/**
	 * @var \Doctrine\ORM\EntityManager
	 * @Inject EntityManager
	 */
	protected $em;

	/**
	 * @var \Entity\Repository\CampRepository
	 * @Inject CampRepository
	 */
	private $campRepo;
	
	/**
     * @var Entity\Repository\UserRepository
     * @Inject UserRepository
     */
    private $userRepo;

	/**
     * @var Entity\Repository\UserCampRepository
     * @Inject UserCampRepository
     */
    private $userCampRepo;


	public function init()
	{
		$this->view->addHelperPath(APPLICATION_PATH . '/../application/views/helpers', 'Application\View\Helper\\');
		
		\Zend_Registry::get('kernel')->InjectDependencies($this);
	}


    public function __construct()
    {
    }

	public function getAllUsers()
	{
		return $this->userRepo->findAll();
	}

	public function addUserToCamp($user,$camp)
	{
		/* besser: via Model lösen, z.B. user->doIBelongToCamp */
		$res = $this->userCampRepo->findBy(array('user' => $user->getId(), 'camp' => $camp->getId() ));

		if( $res == null )
		{
			$userCamp = new \Entity\UserCamp();
			$userCamp->setUser($user);
			$userCamp->setCamp($camp);

			$this->em->persist($userCamp);
			$this->em->flush();
		}
	}

}
