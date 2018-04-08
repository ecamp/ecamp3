<?php

namespace eCamp\ApiTest;

use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;

class AuthTest extends AbstractHttpControllerTestCase
{
    public function testGoogle()
    {
        $this->dispatch("/api/login/google");
        $this->assertRedirect();
        $this->assertRedirectRegex('/^\/auth\/google.*$/');
    }
}
