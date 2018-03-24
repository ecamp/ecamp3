<?php

namespace eCamp\Web\Controller;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Service\CampService;
use eCamp\Lib\Auth\AuthRequiredException;
use Zend\View\Model\ViewModel;

class CampsController extends AbstractBaseController
{
    /** @var AuthService */
    private $authService;

    /** @var CampService */
    private $campService;


    public function __construct
    ( AuthService $authService
    , CampService $campService
    ) {
        $this->authService = $authService;
        $this->campService = $campService;
    }

    /**
     * @return array|ViewModel
     * @throws AuthRequiredException
     * @throws \eCamp\Lib\Acl\NoAccessException
     */
    public function indexAction() {
        $this->forceLogin();

        $camps = $this->campService->fetchAll();

        return [
            'camps' => $camps,
        ];
    }

}
