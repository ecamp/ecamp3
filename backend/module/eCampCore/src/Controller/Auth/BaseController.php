<?php

namespace eCamp\Core\Controller\Auth;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use eCamp\Core\EntityService\UserIdentityService;
use eCamp\Core\EntityService\UserService;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Auth\OAuthAdapter;
use Hybridauth\Adapter\AdapterInterface;
use Hybridauth\Exception\InvalidArgumentException;
use Hybridauth\Exception\UnexpectedValueException;
use Hybridauth\Hybridauth;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

abstract class BaseController extends AbstractActionController {
    public const SESSION_NAMESPACE = self::class;


    /** @var EntityManager */
    protected $entityManager;

    /** @var UserIdentityService */
    protected $userIdentityService;

    /** @var UserService */
    protected $userService;

    /** @var AuthenticationService */
    protected $authenticationService;

    /** @var string */
    protected $providerName;

    /** @var array */
    protected $hybridAuthConfig;


    /** @var Container */
    private $sessionContainer;

    /** @var AdapterInterface */
    private $authAdapter;



    public function __construct(
        EntityManager $entityManager,
        UserIdentityService $userIdentityService,
        UserService $userService,
        AuthenticationService $authenticationService,
        string $providerName,
        array $hybridAuthConfig
    ) {
        $this->entityManager = $entityManager;
        $this->userIdentityService = $userIdentityService;
        $this->userService = $userService;
        $this->authenticationService = $authenticationService;
        $this->providerName = $providerName;
        $this->hybridAuthConfig = $hybridAuthConfig;
    }


    /**
     * @return Response|ViewModel
     * @throws NoAccessException
     * @throws ORMException
     * @throws \Exception
     */
    public function indexAction() {
        /** @var Request $request */
        $request = $this->getRequest();
        $this->setRedirect($request->getQuery('redirect'));

        $this->getAuthAdapter()->disconnect();

        if ($this->getAuthAdapter()->authenticate()) {
            // We were already authenticated, skip to callback
            return $this->callbackAction();
        }

        // We were not authenticated, but the OAuth redirect also didn't happen...
        /** @var Response $response */
        $response = $this->getResponse();
        $response->setStatusCode(401);
        return $response;
    }

    /**
     * @return Response
     * @throws ORMException
     * @throws \Exception
     * @throws NoAccessException
     */
    public function callbackAction() {
        // Perform the second step of OAuth2 authentication
        $this->getAuthAdapter()->authenticate();

        // Get information about the authenticated user
        $profile = $this->getAuthAdapter()->getUserProfile();

        $user = $this->userIdentityService->findOrCreateUser($this->providerName, $profile);

        $result = $this->authenticationService->authenticate(new OAuthAdapter($user->getId()));

        if ($result->isValid()) {
            $redirect = $this->getRedirect();
            if (isset($redirect)) {
                return $this->redirect()->toUrl($redirect);
            }
        }

        // Authentication or redirection failed in some way
        /** @var Response $response */
        $response = $this->getResponse();
        $response->setStatusCode(401);
        return $response;
    }

    /**
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function logoutAction() {
        $this->authenticationService->clearIdentity();
        $this->getAuthAdapter()->disconnect();

        // TODO: Redirect

        die('logout');
    }


    protected function getRedirect() {
        if ($this->sessionContainer == null) {
            $this->sessionContainer = new Container(self::SESSION_NAMESPACE);
        }
        return $this->sessionContainer->redirect;
    }

    protected function setRedirect($redirect) {
        if ($this->sessionContainer == null) {
            $this->sessionContainer = new Container(self::SESSION_NAMESPACE);
        }
        $this->sessionContainer->redirect = $redirect;
    }


    protected function getCallbackUri($route = null, $params = [], $options = []) {
        /** @var Request $request */
        $request = $this->getRequest();

        $uri = $request->getUri();
        $port = $uri->getPort();
        $callbackUri = sprintf('%s://%s', $uri->getScheme(), $uri->getHost());
        if ($port !== null) {
            $callbackUri .= (':' . $port);
        }
        $callbackUri .= $this->url()->fromRoute($route, $params, $options);

        return $callbackUri;
    }

    /**
     * @return AdapterInterface
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    protected function getAuthAdapter() {
        if ($this->authAdapter == null) {
            $this->authAdapter = $this->createAuthAdapter();
        }
        return $this->authAdapter;
    }

    /**
     * @return AdapterInterface
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    protected function createAuthAdapter() {
        $route = $this->getCallbackRoute();
        $callback = $this->getCallbackUri($route, [ 'action' => 'callback' ]);
        $config = ['provider' => $this->providerName, 'callback' => $callback];

        $hybridAuthConfig = $this->hybridAuthConfig + $config;
        $hybridAuth = new Hybridauth($hybridAuthConfig);
        return $hybridAuth->getAdapter($this->providerName);
    }

    /** @return string */
    abstract protected function getCallbackRoute();
}
