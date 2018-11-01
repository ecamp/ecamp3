<?php

namespace eCamp\Web\Controller;

use eCamp\Lib\Auth\AuthRequiredException;
use Zend\View\Model\ViewModel;
use eCamp\Core\EntityService\CampService;

class CampsController extends AbstractBaseController {
    /** @var CampService */
    protected $campService;

    /** @var CampService $campService */
    public function __construct(CampService $campService) {
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
