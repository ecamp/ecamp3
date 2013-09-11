<?php

namespace EcampWeb\Controller;

class IndexController
    extends BaseController
{

    /**
     *
     */
    private function getCampRepository()
    {
        $this->getServiceLocator()->get('EcampCore\Repository\Camp');
    }

    public function indexAction()
    {
        return array();
    }

}
