<?php

namespace EcampWeb\Controller\Group;

class CampsController
    extends BaseController
{

    /**
     * @return \EcampCore\Repository\CampRepository
     */
    private function campRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Camp');
    }

    public function indexAction()
    {
        $camps = $this->campRepository()->findGroupCamps($this->getGroup()->getId());

        return array('camps' => $camps );
    }

}
