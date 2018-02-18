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
     */
    public function indexAction() {
        $this->forceLogin();

        $user = $this->authService->getAuthUser();

        $campsByOwner = $this->campService->fetchByOwner($user);

        return [
            'campsByOwner' => $campsByOwner,
        ];
    }

}
