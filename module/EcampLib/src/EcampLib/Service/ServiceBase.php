<?php

namespace EcampLib\Service;

use Doctrine\ORM\EntityManager;

use EcampCore\Entity\User;
use EcampCore\ServiceUtil\ServiceWrapper;
use EcampLib\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;

abstract class ServiceBase
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    protected function persist($entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    protected function remove($entity)
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * @var EcampLib\Acl\Acl
     */
    private $acl;

    /**
     * @return \EcampLib\Acl\Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }

    public function setAcl(Acl $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @var EcampCore\Entity\User
     */
    private $me = null;

    /**
     * @return EcampCore\Entity\User
     */
    public function getMe()
    {
        return $this->me;
    }

    public function setMe(User $me)
    {
        $this->me = $me;
    }

    /**
     * @param User       $user
     * @param BaseEntity $entity
     * @param $privilege
     * @throws EcampCore\Acl\Exception\NoAccessException
     */
    protected function aclRequire(ResourceInterface $resource = null, $privilege = null)
    {
        $user = $this->getMe() ?: User::ROLE_GUEST;
        $this->getAcl()->isAllowedException($user, $resource, $privilege);
    }

    protected function aclIsAllowed(ResourceInterface $resource = null, $privilege = null)
    {
        $user = $this->getMe() ?: User::ROLE_GUEST;

        return $this->getAcl()->isAllowed($user, $resource, $privilege);
    }

    protected function validationFailed($bool = true, $message = null)
    {
        if ($bool && $message == null) {
            ServiceWrapper::validationFailed();
        }

        if ($bool && $message != null) {
            ServiceWrapper::addValidationMessage($message);
        }
    }

    protected function validationAssert($bool = false, $message = null)
    {
        if(!$bool && $message == null)
            ServiceWrapper::validationFailed();

        if(!$bool && $message != null)
            ServiceWrapper::addValidationMessage($message);
    }

    protected function addValidationMessage($message)
    {
        ServiceWrapper::addValidationMessage($message);
    }

    protected function hasFailed()
    {
        return ServiceWrapper::hasFailed();
    }

}
