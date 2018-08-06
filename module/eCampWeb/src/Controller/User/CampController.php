<?php

namespace eCamp\Web\Controller\User;

use eCamp\Core\EntityServiceAware\CampServiceAware;
use eCamp\Core\EntityServiceTrait\CampServiceTrait;
use eCamp\Web\Controller\AbstractBaseController;

class CampController extends AbstractBaseController
    implements CampServiceAware {
    use CampServiceTrait;

    /**
     * @return array|\Zend\View\Model\ViewModel
     * @throws \eCamp\Lib\Acl\NoAccessException
     */
    public function indexAction() {
        $user = $this->params()->fromRoute('user');
        $camps = $this->getCampService()->fetchAll(['user' => $user]);

        return [
            'user' => $user,
            'camps' => $camps
        ];
    }
}
