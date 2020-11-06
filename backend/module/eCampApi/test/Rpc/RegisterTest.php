<?php

namespace eCamp\ApiTest\Rpc;

use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class RegisterTest extends AbstractApiControllerTestCase {
    public function testRegisterWithoutUsername() {
        $this->setRequestContent(['username' => '']);
        $this->dispatch('/api/register', 'POST');

        $this->assertResponseStatusCode(400);
        $this->assertContains('No username', $this->getResponseContent()->detail);
    }

    public function testRegisterWithoutEmail() {
        $this->setRequestContent([
            'username' => 'test',
            'email' => '',
            'password' => '', ]);
        $this->dispatch('/api/register', 'POST');

        $this->assertResponseStatusCode(400);
        $this->assertContains('No eMail', $this->getResponseContent()->detail);
    }

    public function testRegisterWithoutPassword() {
        $this->setRequestContent([
            'username' => 'test',
            'email' => 'test@test.com',
            'password' => '', ]);
        $this->dispatch('/api/register', 'POST');

        $this->assertResponseStatusCode(400);
        $this->assertContains('No password', $this->getResponseContent()->detail);
    }

    public function testRegisterSuccess() {
        $this->setRequestContent([
            'username' => 'test',
            'email' => 'test@test.com',
            'password' => '12345', ]);
        $this->dispatch('/api/register', 'POST');

        $this->assertResponseStatusCode(200);

        $response = $this->getResponseContent();
        $this->assertEquals($response->username, 'test');
        $this->assertEquals($response->role, 'user');
    }
}
