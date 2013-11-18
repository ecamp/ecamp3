<?php

namespace EcampWeb\Controller\Camp;

class IndexController
    extends BaseController
{
    /**
     * @return \EcampCore\Service\PeriodService
     */
    private function getPeriodService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\period');
    }

    public function indexAction()
    {
        $period = $this->getCamp()->getPeriods();

        return array();
    }

    public function addPeriodAction()
    {
        $this->getPeriodService()->Create($this->getCamp(),null);

        return array();
    }

}
