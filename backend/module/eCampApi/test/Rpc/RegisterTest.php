<?php

namespace eCamp\ApiTest\Rpc;

use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class RegisterTest extends AbstractApiControllerTestCase {
    public function testRegisterWithoutUsername(): void {
        $this->setRequestContent(['username' => '']);
        $this->dispatch('/api/register', 'POST');

        $this->assertResponseStatusCode(400);
        $this->assertStringContainsString('No username', $this->getResponseContent()->detail);
    }

    public function testRegisterWithoutEmail(): void {
        $this->setRequestContent([
            'username' => 'test',
            'email' => '',
            'password' => '', ]);
        $this->dispatch('/api/register', 'POST');

        $this->assertResponseStatusCode(400);
        $this->assertStringContainsString('No email', $this->getResponseContent()->detail);
    }

    public function testRegisterWithoutPassword(): void {
        $this->setRequestContent([
            'username' => 'test',
            'email' => 'test@test.com',
            'password' => '', ]);
        $this->dispatch('/api/register', 'POST');

        $this->assertResponseStatusCode(400);
        $this->assertStringContainsString('No password', $this->getResponseContent()->detail);
    }

    public function testRegisterSuccess(): void {
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
