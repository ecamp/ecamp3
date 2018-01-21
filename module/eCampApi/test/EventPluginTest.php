<?php

namespace eCamp\ApiTest;

use Zend\Http\Request;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class EventPluginTest extends  AbstractHttpControllerTestCase
{

    public function setUp() {
        $data = include __DIR__ . '/../../../config/application.config.php';
        $this->setApplicationConfig($data);

        parent::setUp();
    }

    public function testCreateEventPlugin() {
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

        $this->dispatch("http://localhost:8888/api/event_plugin", 'POST');
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();

    }

}
