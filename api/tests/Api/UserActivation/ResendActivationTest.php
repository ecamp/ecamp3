<?php

namespace App\Tests\Api\UserActivation;

use ApiPlatform\Metadata\Post;
use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ResendActivationTest extends ECampApiTestCase {
    public function testPostResendActivationSendsActivationEmail() {
        $client = static::createBasicClient();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        // register user
        $result = $client->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWriteUserPayload(),
            ]
        );
        $this->assertResponseStatusCodeSame(201);

        $userId = $result->toArray()['id'];
        $user = $this->getEntityManager()->getRepository(User::class)->find($userId);

        $client->request(
            'POST',
            '/auth/resend_activation',
            [
                'json' => [
                    'email' => $user->getEmail(),
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(204);
        self::assertEmailCount(1);
        $mailerMessage = self::getMailerMessage();
        self::assertEmailAddressContains($mailerMessage, 'To', $user->getEmail());
        self::assertEmailHeaderSame($mailerMessage, 'subject', 'Welcome to eCamp v3');

        self::assertEmailHtmlBodyContains($mailerMessage, $user->getDisplayName());
        self::assertEmailHtmlBodyContains($mailerMessage, '/activate');

        // and use activation code
        $user = $this->getEntityManager()->getRepository(User::class)->find($userId);

        // activate user
        $client->request('PATCH', "/users/{$userId}/activate", ['json' => [
            'activationKey' => $user->activationKey,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseIsSuccessful();

        // login
        $client->request('POST', '/authentication_token', ['json' => [
            'identifier' => 'bi-pi@example.com',
            'password' => 'learning-by-doing-101',
        ]]);
        $this->assertResponseIsSuccessful();
    }

    public function testPostResendActivationTrimsEmail() {
        $client = static::createBasicClient();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        // register user
        $result = $client->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWriteUserPayload(),
            ]
        );
        $this->assertResponseStatusCodeSame(201);

        $userId = $result->toArray()['id'];
        $user = $this->getEntityManager()->getRepository(User::class)->find($userId);

        $client->request(
            'POST',
            '/auth/resend_activation',
            [
                'json' => [
                    'email' => " {$user->getEmail()}\t ",
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(204);
        self::assertEmailCount(1);
    }

    public function testPostResendActivationInvalidatesPreviousValidationKey() {
        $client = static::createBasicClient();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        // register user
        $result = $client->request(
            'POST',
            '/users',
            [
                'json' => $this->getExampleWriteUserPayload(),
            ]
        );
        $this->assertResponseStatusCodeSame(201);

        $userId = $result->toArray()['id'];
        $user = $this->getEntityManager()->getRepository(User::class)->find($userId);

        $activationKey = 'myActivationKey';
        $user->activationKeyHash = md5($activationKey);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $client->request(
            'POST',
            '/auth/resend_activation',
            [
                'json' => [
                    'email' => " {$user->getEmail()}\t ",
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(204);
        self::assertEmailCount(1);

        // activate user
        $client->request('PATCH', "/users/{$userId}/activate", ['json' => [
            'activationKey' => $activationKey,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testPostResendActivationReturns204ForUnknownEmailButSendsNoEmails() {
        $this->createBasicClient()->request(
            'POST',
            '/auth/resend_activation',
            [
                'json' => [
                    'email' => 'a@b.com',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(204);
        self::assertEmailCount(0);
    }

    public function getExampleWriteUserPayload($attributes = [], $except = [], $mergeEmbeddedAttributes = []) {
        $examplePayload = $this->getExamplePayload(
            User::class,
            Post::class,
            $attributes,
            [],
            $except
        );

        return array_replace_recursive($examplePayload, $mergeEmbeddedAttributes);
    }
}
