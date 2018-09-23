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

        // This will lazy load the eventCategory Service
        //var_dump("CampsController: Now going to do something with category Service.");
        //$this->campService->doSomethingWithCategory();

        return [
            'camps' => $camps,
        ];
    }
}
