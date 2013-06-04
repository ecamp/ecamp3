<?php

namespace EcampCore\Controller;

use EcampLib\Controller\AbstractBaseController;
use DoctrineModule\Paginator\Adapter\Selectable;

class DemoController
    extends AbstractBaseController
{
    /**
     * @return \EcampCore\Repository\UserRepository
     */
    private function getUserRepo()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\User');
    }

    /**
     * @return \EcampCore\Repository\CampRepository
     */
    private function getCampRepo()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Camp');
    }

    public function paginatorAction()
    {
        $adapter = new Selectable($objectRepository, $criteria);

    }

    public function varAction()
    {
        $users = $this->getUserRepo()->findAll();
        $userCampData = array();

        foreach ($users as $k => $user) {
            $userCampData[$k] = array(
                'user' => $user,
                'camps' => $this->getCampRepo()->findPersonalCamps($user->getId())
            );
        }

        return array(
            'user_camp_data' => $userCampData
        );
    }

}
