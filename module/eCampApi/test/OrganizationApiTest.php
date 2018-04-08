<?php

namespace eCamp\ApiTest;

use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;

class OrganizationApiTest extends AbstractHttpControllerTestCase
{
    public function testOrganizationFetch()
    {
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');

        $this->dispatch("/api/organization");
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();
    }
}
