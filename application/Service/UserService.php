<?php

namespace Service;
use Zend_Registry;
	
class UserService
{

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

    /**
     * @var Bisna\Application\Container\DoctrineContainer
     */
    protected $doctrine;

    /**
     * @var Entity\Repository\*
     */
    protected $campRepo;
    protected $loginRepo;
	protected $userRepo;


    public function __construct()
    {
	    $this->doctrine = Zend_Registry::get('doctrine');
        $this->em = $this->doctrine->getEntityManager();

        $this->campRepo  = $this->em->getRepository('\Entity\Camp');
        $this->loginRepo = $this->em->getRepository('\Entity\Login');
	    $this->userRepo = $this->em->getRepository('\Entity\User');
    }

	public function getAllUsers()
	{
		return $this->userRepo->findAll();
	}

	public function addUserToCamp($user,$camp)
	{
		/* besser: via Model lösen, z.B. user->doIBelongToCamp */
		
		$res = $this->em->getRepository('\Entity\UserToCamp')->findBy(array('user' => $user->getId(), 'camp' => $camp->getId() ));

		if( $res == null )
		{
			$userCamp = new \Entity\UserToCamp();
			$userCamp->setUser($user);
			$userCamp->setCamp($camp);

			$this->em->persist($userCamp);
			$this->em->flush();
		}
	}

}
