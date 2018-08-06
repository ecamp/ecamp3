<?php

namespace eCamp\Web\Controller;

use eCamp\Core\EntityServiceAware\CampServiceAware;
use eCamp\Core\EntityServiceTrait\CampServiceTrait;
use eCamp\Lib\Auth\AuthRequiredException;
use Zend\View\Model\ViewModel;

class CampsController extends AbstractBaseController
    implements CampServiceAware {
    use CampServiceTrait;


    /**
     * @return array|ViewModel
     * @throws AuthRequiredException
     * @throws \eCamp\Lib\Acl\NoAccessException
     */
    public function indexAction() {
        $this->forceLogin();

        $camps = $this->getCampService()->fetchAll();

        return [
            'camps' => $camps,
        ];
    }
}
