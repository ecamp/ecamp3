<?php
// http://localhost:8888/api/event_template/6105e57

namespace eCamp\ApiTest;

use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;

class EventTemplateApiTest extends AbstractHttpControllerTestCase
{

    public function testOrganizationFetch() {
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');

        $this->dispatch("/api/event_template/6105e57");
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();

    }
}
