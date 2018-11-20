<?php

namespace eCamp\Core\Controller\Auth;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use eCamp\Core\Auth\Adapter\JwtPayload;
use eCamp\Core\EntityService\UserIdentityService;
use eCamp\Core\EntityService\UserService;
use eCamp\Lib\Acl\NoAccessException;
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
    protected $zendAuthenticationService;

    /** @var string */
    protected $providerName;

    /** @var array */
    protected $hybridAuthConfig;


    /** @var Container */
    private $sessionContainer;

    /** @var AdapterInterface */
    private $hybridAuthAdapter;



    public function __construct(
        EntityManager $entityManager,
        UserIdentityService $userIdentityService,
        UserService $userService,
        AuthenticationService $zendAuthenticationService,
        string $providerName,
        array $hybridAuthConfig
    ) {
        $this->entityManager = $entityManager;
        $this->userIdentityService = $userIdentityService;
        $this->userService = $userService;
        $this->zendAuthenticationService = $zendAuthenticationService;
        $this->providerName = $providerName;
        $this->hybridAuthConfig = $hybridAuthConfig;
    }


    /**
     * @return Response|ViewModel
     * @throws NoAccessException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \Exception
     */
    public function indexAction() {
        /** @var Request $request */
        $request = $this->getRequest();
        $this->setRedirect($request->getQuery('redirect'));

        $this->getHybridauthAdapter()->disconnect();

        if ($this->getHybridauthAdapter()->authenticate()) {
            // We were already authenticated, skip to callback
            return $this->callbackAction();
        }

        // Authentication failed in some way
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
        $this->getHybridauthAdapter()->authenticate();

        // Get information about the authenticated user
        $profile = $this->getHybridauthAdapter()->getUserProfile();

        $user = $this->userIdentityService->findOrCreateUser($this->providerName, $profile);

        // Send proof of authentication in a signed JWT token to the user
        $result = $this->zendAuthenticationService->authenticate(new JwtPayload($user->getId(), $user->getRole()));

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
        $this->zendAuthenticationService->clearIdentity();
        $this->getHybridauthAdapter()->disconnect();

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
    protected function getHybridauthAdapter() {
        if ($this->hybridAuthAdapter == null) {
            $this->hybridAuthAdapter = $this->createHybridauthAdapter();
        }
        return $this->hybridAuthAdapter;
    }

    /**
     * @return AdapterInterface
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    protected function createHybridauthAdapter() {
        $route = $this->getCallbackRoute();
        $callback = $this->getCallbackUri($route, [ 'action' => 'callback' ]);
        $config = ['provider' => $this->providerName, 'callback' => $callback];

        $hybridAuthConfig = $this->hybridAuthConfig + $config;
        $hybridauth = new Hybridauth($hybridAuthConfig);

        return $hybridauth->getAdapter($this->providerName);
    }

    /** @return string */
    abstract protected function getCallbackRoute();
}
