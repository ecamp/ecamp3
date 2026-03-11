<?php

namespace App\Tests\OAuth;

use App\Entity\OAuthState;
use App\OAuth\Hitobito;
use App\OAuth\JWTStateOAuth2Client;
use App\Repository\OAuthStateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use KnpU\OAuth2ClientBundle\Exception\InvalidStateException;
use League\OAuth2\Client\Token\AccessToken;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
class JWTStateOAuth2ClientTest extends TestCase {
    public function testGetCookieName() {
        // given

        // when
        $cookieName = JWTStateOAuth2Client::getCookieName('test_com_');

        // then
        $this->assertSame('test_com_oauth_state_jwt', $cookieName);
    }

    public function testRedirect() {
        // given
        $providerMock = $this->createStub(Hitobito::class);
        $providerMock->method('getAuthorizationUrl')->willReturn('/some-url');

        $requestStackMock = $this->createStub(RequestStack::class);

        $jwtEncoderMock = $this->createMock(JWTEncoderInterface::class);
        $jwtEncoderMock->expects($this->once())
            ->method('encode')
            ->with($this->callback(function (array $value) {
                return is_array($value)
                    && 'bar' === $value['foo']
                    && is_string($value['state'])
                    && is_int($value['iat'])
                    && is_int($value['exp'])
                    && ($value['iat'] < $value['exp']);
            }))
            ->willReturn('my encoded JWT value')
        ;

        $entityManagerMock = $this->createStub(EntityManagerInterface::class);

        $repositoryMock = $this->createStub(OAuthStateRepository::class);

        $client = new JWTStateOAuth2Client(
            $providerMock,
            $requestStackMock,
            'test_prefix_',
            'prod',
            $jwtEncoderMock,
            $entityManagerMock,
            $repositoryMock,
        );

        // when
        $response = $client->redirect([], ['additionalData' => ['foo' => 'bar']]);

        // then
        $this->assertCount(1, $response->headers->getCookies());
        $this->assertEquals('test_prefix_oauth_state_jwt', $response->headers->getCookies()[0]->getName());
        $this->assertEquals('lax', $response->headers->getCookies()[0]->getSameSite());
        $this->assertTrue($response->headers->getCookies()[0]->isHttpOnly());
        $this->assertTrue($response->headers->getCookies()[0]->isSecure());
        $this->assertEquals('my encoded JWT value', $response->headers->getCookies()[0]->getValue());
    }

    public function testRedirectPersistsStateToTheDatabase() {
        // given
        $providerMock = $this->createStub(Hitobito::class);
        $providerMock->method('getAuthorizationUrl')->willReturn('/some-url');

        $requestStackMock = $this->createStub(RequestStack::class);

        $jwtEncoderMock = $this->createStub(JWTEncoderInterface::class);
        $jwtEncoderMock->method('encode')->willReturn('my encoded JWT value');

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $repositoryMock = $this->createMock(OAuthStateRepository::class);

        $client = new JWTStateOAuth2Client(
            $providerMock,
            $requestStackMock,
            'test_prefix_',
            'prod',
            $jwtEncoderMock,
            $entityManagerMock,
            $repositoryMock,
        );

        // then
        $entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(OAuthState::class))
        ;
        // Should also clean up the database table
        $repositoryMock->expects($this->once())->method('deleteAllExpiredBefore');

        // when
        $client->redirect([], ['additionalData' => ['foo' => 'bar']]);
    }

    public function testGetAccessToken() {
        // given
        $state = 'test value';

        $providerMock = $this->createStub(Hitobito::class);
        $providerMock->method('getAccessToken')->willReturn(new AccessToken(['access_token' => 'test access token']));

        $requestMock = $this->createStub(Request::class);
        $requestStackMock = $this->createStub(RequestStack::class);
        $requestStackMock->method('getCurrentRequest')->willReturn($requestMock);
        $cookieBag = new InputBag();
        $cookieBag->set('test_prefix_oauth_state_jwt', 'test jwt value');
        $requestMock->cookies = $cookieBag;
        $requestMock->query = new InputBag(['state' => $state, 'code' => $state]);

        $jwtEncoderMock = $this->createMock(JWTEncoderInterface::class);
        $jwtEncoderMock->expects($this->once())
            ->method('decode')
            ->with('test jwt value')
            ->willReturn(['state' => $state])
        ;

        $repositoryMock = $this->createStub(OAuthStateRepository::class);
        $oAuthState = new OAuthState();
        $repositoryMock->method('findOneUnexpiredBy')->willReturn($oAuthState);

        $entityManagerMock = $this->createStub(EntityManagerInterface::class);

        $client = new JWTStateOAuth2Client(
            $providerMock,
            $requestStackMock,
            'test_prefix_',
            'prod',
            $jwtEncoderMock,
            $entityManagerMock,
            $repositoryMock,
        );

        // when
        $accessToken = $client->getAccessToken([]);

        // then
        $this->assertEquals('test access token', $accessToken->getToken());
    }

    public function testGetAccessTokenThrowsIfJWTCannotBeDecoded() {
        // given
        $state = 'test value';

        $providerMock = $this->createStub(Hitobito::class);
        $providerMock->method('getAccessToken')->willReturn(new AccessToken(['access_token' => 'test access token']));

        $requestMock = $this->createStub(Request::class);
        $requestStackMock = $this->createStub(RequestStack::class);
        $requestStackMock->method('getCurrentRequest')->willReturn($requestMock);
        $cookieBag = new InputBag();
        $cookieBag->set('test_prefix_oauth_state_jwt', 'test jwt value');
        $requestMock->cookies = $cookieBag;
        $requestMock->query = new InputBag(['state' => $state, 'code' => $state]);

        $jwtEncoderMock = $this->createMock(JWTEncoderInterface::class);
        $jwtEncoderMock->expects($this->once())
            ->method('decode')
            ->with('test jwt value')
            ->willThrowException(new JWTDecodeFailureException('', ''))
        ;

        $repositoryMock = $this->createStub(OAuthStateRepository::class);
        $oAuthState = new OAuthState();
        $repositoryMock->method('findOneUnexpiredBy')->willReturn($oAuthState);

        $entityManagerMock = $this->createStub(EntityManagerInterface::class);

        $client = new JWTStateOAuth2Client(
            $providerMock,
            $requestStackMock,
            'test_prefix_',
            'prod',
            $jwtEncoderMock,
            $entityManagerMock,
            $repositoryMock,
        );

        // then
        $this->expectExceptionObject(new InvalidStateException('Invalid state'));

        // when
        $client->getAccessToken([]);
    }

    public function testGetAccessTokenThrowsIfJWTStateDoesNotMatch() {
        // given
        $state = 'test value';

        $providerMock = $this->createStub(Hitobito::class);
        $providerMock->method('getAccessToken')->willReturn(new AccessToken(['access_token' => 'test access token']));

        $requestMock = $this->createStub(Request::class);
        $requestStackMock = $this->createStub(RequestStack::class);
        $requestStackMock->method('getCurrentRequest')->willReturn($requestMock);
        $cookieBag = new InputBag();
        $cookieBag->set('test_prefix_oauth_state_jwt', 'test jwt value');
        $requestMock->cookies = $cookieBag;
        $requestMock->query = new InputBag(['state' => $state, 'code' => $state]);

        $jwtEncoderMock = $this->createMock(JWTEncoderInterface::class);
        $jwtEncoderMock->expects($this->once())
            ->method('decode')
            ->with('test jwt value')
            ->willReturn(['state' => 'something unexpected'])
        ;

        $repositoryMock = $this->createStub(OAuthStateRepository::class);
        $oAuthState = new OAuthState();
        $repositoryMock->method('findOneUnexpiredBy')->willReturn($oAuthState);

        $entityManagerMock = $this->createStub(EntityManagerInterface::class);

        $client = new JWTStateOAuth2Client(
            $providerMock,
            $requestStackMock,
            'test_prefix_',
            'prod',
            $jwtEncoderMock,
            $entityManagerMock,
            $repositoryMock,
        );

        // then
        $this->expectExceptionObject(new InvalidStateException('Invalid state'));

        // when
        $client->getAccessToken([]);
    }

    public function testGetAccessTokenThrowsIfNoMatchingStateEntryInTheDatabase() {
        // given
        $state = 'test value';

        $providerMock = $this->createMock(Hitobito::class);
        $providerMock->method('getAccessToken')->willReturn(new AccessToken(['access_token' => 'test access token']));

        $requestMock = $this->createStub(Request::class);
        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->method('getCurrentRequest')->willReturn($requestMock);
        $cookieBag = new InputBag();
        $cookieBag->set('test_prefix_oauth_state_jwt', 'test jwt value');
        $requestMock->cookies = $cookieBag;
        $requestMock->query = new InputBag(['state' => $state, 'code' => $state]);

        $jwtEncoderMock = $this->createMock(JWTEncoderInterface::class);
        $jwtEncoderMock->expects($this->once())
            ->method('decode')
            ->with('test jwt value')
            ->willReturn(['state' => $state])
        ;

        $repositoryMock = $this->createMock(OAuthStateRepository::class);
        $repositoryMock->method('findOneUnexpiredBy')
            ->willThrowException(new NoResultException())
        ;

        $entityManagerMock = $this->createStub(EntityManagerInterface::class);

        $client = new JWTStateOAuth2Client(
            $providerMock,
            $requestStackMock,
            'test_prefix_',
            'prod',
            $jwtEncoderMock,
            $entityManagerMock,
            $repositoryMock,
        );

        // then
        $this->expectExceptionObject(new InvalidStateException('Invalid state'));

        // when
        $client->getAccessToken([]);
    }

    public function testGetAccessTokenThrowsIfMultipleMatchingStateEntriesInTheDatabase() {
        // given
        $state = 'test value';

        $providerMock = $this->createMock(Hitobito::class);
        $providerMock->method('getAccessToken')->willReturn(new AccessToken(['access_token' => 'test access token']));

        $requestMock = $this->createStub(Request::class);
        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->method('getCurrentRequest')->willReturn($requestMock);
        $cookieBag = new InputBag();
        $cookieBag->set('test_prefix_oauth_state_jwt', 'test jwt value');
        $requestMock->cookies = $cookieBag;
        $requestMock->query = new InputBag(['state' => $state, 'code' => $state]);

        $jwtEncoderMock = $this->createMock(JWTEncoderInterface::class);
        $jwtEncoderMock->expects($this->once())
            ->method('decode')
            ->with('test jwt value')
            ->willReturn(['state' => $state])
        ;

        $repositoryMock = $this->createMock(OAuthStateRepository::class);
        $repositoryMock->method('findOneUnexpiredBy')
            ->willThrowException(new NonUniqueResultException())
        ;

        $entityManagerMock = $this->createStub(EntityManagerInterface::class);

        $client = new JWTStateOAuth2Client(
            $providerMock,
            $requestStackMock,
            'test_prefix_',
            'prod',
            $jwtEncoderMock,
            $entityManagerMock,
            $repositoryMock,
        );

        // then
        $this->expectExceptionObject(new InvalidStateException('Invalid state'));

        // when
        $client->getAccessToken([]);
    }

    public function testGetAccessTokenRemovesSavedStateFromDatabase() {
        // given
        $state = 'test value';

        $providerMock = $this->createMock(Hitobito::class);
        $providerMock->method('getAccessToken')->willReturn(new AccessToken(['access_token' => 'test access token']));

        $requestMock = $this->createStub(Request::class);
        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->method('getCurrentRequest')->willReturn($requestMock);
        $cookieBag = new InputBag();
        $cookieBag->set('test_prefix_oauth_state_jwt', 'test jwt value');
        $requestMock->cookies = $cookieBag;
        $requestMock->query = new InputBag(['state' => $state, 'code' => $state]);

        $jwtEncoderMock = $this->createMock(JWTEncoderInterface::class);
        $jwtEncoderMock->expects($this->once())
            ->method('decode')
            ->with('test jwt value')
            ->willReturn(['state' => $state])
        ;

        $repositoryMock = $this->createMock(OAuthStateRepository::class);
        $oAuthState = new OAuthState();
        $repositoryMock->method('findOneUnexpiredBy')->willReturn($oAuthState);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($oAuthState)
        ;

        $client = new JWTStateOAuth2Client(
            $providerMock,
            $requestStackMock,
            'test_prefix_',
            'prod',
            $jwtEncoderMock,
            $entityManagerMock,
            $repositoryMock,
        );

        // when
        $client->getAccessToken([]);

        // then
    }
}
