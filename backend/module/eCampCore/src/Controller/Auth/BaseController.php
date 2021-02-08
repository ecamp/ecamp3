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
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;

abstract class BaseController extends AbstractActionController {
    public const SESSION_NAMESPACE = self::class;

    protected EntityManager $entityManager;
    protected UserIdentityService $userIdentityService;
    protected UserService $userService;
    protected AuthenticationService $laminasAuthenticationService;
    protected string $providerName;
    protected array $hybridAuthConfig;
    private ?Container $sessionContainer;
    private ?AdapterInterface $hybridAuthAdapter;

    public function __construct(
        EntityManager $entityManager,
        UserIdentityService $userIdentityService,
        UserService $userService,
        AuthenticationService $laminasAuthenticationService,
        string $providerName,
        array $hybridAuthConfig
    ) {
        $this->entityManager = $entityManager;
        $this->userIdentityService = $userIdentityService;
        $this->userService = $userService;
        $this->laminasAuthenticationService = $laminasAuthenticationService;
        $this->providerName = $providerName;
        $this->hybridAuthConfig = $hybridAuthConfig;
        $this->sessionContainer = null;
        $this->hybridAuthAdapter = null;
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     * @throws \Exception
     *
     * @return Response|ViewModel
     */
    public function indexAction(): Response {
        /** @var Request $request */
        $request = $this->getRequest();
        $this->setRedirect($request->getQuery('redirect'));

        $this->getHybridAuthAdapter()->disconnect();

        if ($this->getHybridAuthAdapter()->authenticate()) {
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
     * @throws ORMException
     * @throws \Exception
     * @throws NoAccessException
     */
    public function callbackAction(): Response {
        // Perform the second step of OAuth2 authentication
        $this->getHybridAuthAdapter()->authenticate();

        // Get information about the authenticated user
        $profile = $this->getHybridAuthAdapter()->getUserProfile();
        $user = $this->userIdentityService->findOrCreateUser($this->providerName, $profile);
        $result = $this->laminasAuthenticationService->authenticate(new OAuthAdapter($user->getId()));

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
    public function logoutAction(): void {
        $this->laminasAuthenticationService->clearIdentity();
        $this->getHybridAuthAdapter()->disconnect();

        // TODO: Redirect

        exit('logout');
    }

    protected function getRedirect(): ?string {
        if (null == $this->sessionContainer) {
            $this->sessionContainer = new Container(self::SESSION_NAMESPACE);
        }

        return $this->sessionContainer->redirect;
    }

    protected function setRedirect(?string $redirect): void {
        if (null == $this->sessionContainer) {
            $this->sessionContainer = new Container(self::SESSION_NAMESPACE);
        }
        $this->sessionContainer->redirect = $redirect;
    }

    protected function getCallbackUri($route = null, $params = [], $options = []): string {
        /** @var Request $request */
        $request = $this->getRequest();

        $uri = $request->getUri();
        $port = $uri->getPort();
        $callbackUri = sprintf('%s://%s', $uri->getScheme(), $uri->getHost());
        if (null !== $port) {
            $callbackUri .= (':'.$port);
        }
        $callbackUri .= $this->url()->fromRoute($route, $params, $options);

        return $callbackUri;
    }

    /**
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    protected function getHybridAuthAdapter(): AdapterInterface {
        if (null == $this->hybridAuthAdapter) {
            $this->hybridAuthAdapter = $this->createHybridAuthAdapter();
        }

        return $this->hybridAuthAdapter;
    }

    /**
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    protected function createHybridAuthAdapter(): AdapterInterface {
        $route = $this->getCallbackRoute();
        $callback = $this->getCallbackUri($route, ['action' => 'callback']);
        $config = ['provider' => $this->providerName, 'callback' => $callback];

        $hybridAuthConfig = $this->hybridAuthConfig + $config;
        $hybridAuth = new Hybridauth($hybridAuthConfig);

        return $hybridAuth->getAdapter($this->providerName);
    }

    abstract protected function getCallbackRoute(): string;
}
