<?php

namespace eCamp\ApiTest;

use Zend\Http\Request;
use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;

class EventPluginTe_st extends  AbstractHttpControllerTestCase
{

    public function devTestCreateEventPlugin() {
        $headers = $this->getRequest()->getHeaders();
        $headers->addheaderLine('Content-Type', 'application/json');
        $headers->addHeaderLine('Accept', 'application/json');

        /** @var Request $req */
        $req  = $this->getRequest();
        $req->setContent('{
            "event_id": "48909",
            "event_type_plugin_id": "2b72ec82",
            "instance_name": "mytest3"
        }');

        $this->dispatch("/api/event_plugin", 'POST');
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();
    }

}
