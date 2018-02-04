<?php

namespace eCamp\WebTest;

use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;

class IndexTest extends AbstractHttpControllerTestCase
{

    public function testIndex() {
        $this->dispatch("/", 'GET');
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();
    }

}