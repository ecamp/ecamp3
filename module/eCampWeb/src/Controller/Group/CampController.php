<?php

namespace eCamp\Web\Controller\Group;

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
        $group = $this->params()->fromRoute('group');
        $camps = $this->getCampService()->fetchAll(['group' => $group]);

        return [
            'group' => $group,
            'camps' => $camps
        ];
    }
}
