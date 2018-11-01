<?php

namespace eCamp\Web\Controller\User;

use eCamp\Core\EntityService\CampService;
use eCamp\Web\Controller\AbstractBaseController;

class CampController extends AbstractBaseController {

    private $campService;

    public function __construct(CampService $campService) {
        $this->campService = $campService;
    }

    /**
     * @return array|\Zend\View\Model\ViewModel
     * @throws \eCamp\Lib\Acl\NoAccessException
     */
    public function indexAction() {
        $user = $this->params()->fromRoute('user');
        $camps = $this->campService->fetchAll(['user' => $user]);

        return [
            'user' => $user,
            'camps' => $camps
        ];
    }
}
