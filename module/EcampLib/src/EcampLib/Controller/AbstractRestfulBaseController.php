<?php

namespace EcampLib\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

abstract class AbstractRestfulBaseController extends AbstractRestfulController
{

    protected function createCriteriaArray(array $criteria)
    {
        return array_merge(
            array(
                'offset'	=> $this->params()->fromQuery('offset'),
                'limit'		=> $this->params()->fromQuery('limit'),
            ),
            $criteria
        );
    }

}
