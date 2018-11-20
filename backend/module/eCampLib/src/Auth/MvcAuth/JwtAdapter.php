<?php

namespace eCamp\Lib\Auth\MvcAuth;


use Carnage\JwtZendAuth\Service\Exception\InvalidTokenException;
use Carnage\JwtZendAuth\Service\Exception\TokenSignatureVerificationFailedException;
use Carnage\JwtZendAuth\Service\Exception\TokenValidationFailedException;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Request;
use Zend\Http\Response;
use ZF\MvcAuth\Authentication\AdapterInterface;
use ZF\MvcAuth\Identity\GuestIdentity;
use ZF\MvcAuth\Identity\IdentityInterface;
use ZF\MvcAuth\MvcAuthEvent;

class JwtAdapter implements AdapterInterface {
    /**
     * @return array Array of types this adapter can handle.
     */
    public function provides() {
        return [ "jwt" ];
    }

    /**
     * Attempt to match a requested authentication type
     * against what the adapter provides.
     *
     * @param string $type
     * @return bool
     */
    public function matches($type) {
        return in_array($type, $this->provides(), true);
    }

    /**
     * Attempt to retrieve the authentication type based on the request.
     *
     * Allows an adapter to have custom logic for detecting if a request
     * might be providing credentials it's interested in.
     *
     * @param Request $request
     * @return false|string
     */
    public function getTypeFromRequest(Request $request) {
        if ($this->getJwtTokenFromRequest($request) === false) {
            return false;
        }
        return "jwt";
    }

    /**
     * @param Request $request
     * @return string|null
     */
    private function getJwtTokenFromRequest(Request $request) {
        $authorizationHeader = $request->getHeader('Authorization');
        if (!$authorizationHeader) {
            return false;
        }
        $authHeader = $authorizationHeader->getFieldValue();
        if (!$authHeader) {
            return false;
        }
        if (substr($authHeader, 0, 7) !== 'Bearer ') {
            return false;
        }
        if (substr_count($authHeader, '.') !== 2) {
            return false;
        }
        return substr($authHeader, 7);
    }

    /**
     * Perform pre-flight authentication operations.
     *
     * Use case would be for providing authentication challenge headers.
     *
     * @param Request $request
     * @param Response $response
     * @return void|Response
     */
    public function preAuth(Request $request, Response $response) {}

    /**
     * Attempt to authenticate the current request.
     *
     * @param Request $request
     * @param Response $response
     * @param MvcAuthEvent $mvcAuthEvent
     * @return false|IdentityInterface False on failure, IdentityInterface
     *     otherwise
     */
    public function authenticate(Request $request, Response $response, MvcAuthEvent $mvcAuthEvent) {
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $mvcAuthEvent->getMvcEvent()->getApplication()->getServiceManager()->get(AuthenticationService::class);
        try {
            return new JwtIdentity($authenticationService->getIdentity());
        } catch (TokenValidationFailedException | TokenSignatureVerificationFailedException | InvalidTokenException $e) {
            // TODO invalid jwt token (or expired). Ask the user to log in again
            return new GuestIdentity();
        }
    }
}
