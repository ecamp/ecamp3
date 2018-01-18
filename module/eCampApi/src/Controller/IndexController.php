<?php

namespace eCamp\Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZF\Hal\Entity;
use ZF\Hal\Link\Link;
use ZF\Hal\View\HalJsonModel;

class IndexController extends AbstractActionController
{
    public function indexAction() {

        // Login-Info
        // Login-Endpoint

        // MyCamps
        // My....


        $camps = new Link('camps');
        $camps->setRoute('ecamp.api.camp');

        $root = new Entity([
            'title' => 'eCamp V3 - API',

            'user' => new Entity([
                'username' => 'MyUser'
            ]),

            'camps' => $camps
        ]);

        $json = new HalJsonModel();
        $json->setPayload($root);

        return $json;
    }
}
