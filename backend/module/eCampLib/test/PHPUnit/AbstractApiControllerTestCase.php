<?php

namespace eCamp\LibTest\PHPUnit;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use eCamp\Core\Entity\User;
use Laminas\Authentication\AuthenticationService;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase as ZendAbstractHttpControllerTestCase;

abstract class AbstractApiControllerTestCase extends ZendAbstractHttpControllerTestCase {
    /**
     * @throws ToolsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setUp() {
        parent::setUp();

        $data = include __DIR__.'/../../../../config/application.config.php';
        $this->setApplicationConfig($data);

        $em = $this->getEntityManager();
        $this->createDatabaseSchema($em);

        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');
        $headers->addHeaderLine('Content-Type', 'application/json');
    }

    protected function getEntityManager($name = null) {
        $name = $name ?: 'orm_default';
        $name = 'doctrine.entitymanager.'.$name;

        return $this->getApplicationServiceLocator()->get($name);
    }

    /** @throws ToolsException */
    protected function createDatabaseSchema(EntityManager $em) {
        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas);
    }

    /**
     * Set request content encoded as JSON.
     *
     * @param mixed $content
     */
    protected function setRequestContent($content) {
        $this->getRequest()->setContent(json_encode($content));
    }

    /**
     * Get response content decoded from JSON.
     *
     * @return mixed
     */
    protected function getResponseContent() {
        return json_decode($this->getResponse()->getContent());
    }

    /**
     * Creates a new user and authenticates it as the current user.
     *
     * @return User
     */
    protected function createAndAuthenticateUser() {
        $user = new User();
        $user->setRole(User::ROLE_USER);
        $user->setState(User::STATE_ACTIVATED);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        /** @var AuthenticationService $auth */
        $auth = $this->getApplicationServiceLocator()->get(AuthenticationService::class);
        $auth->getStorage()->write($user->getId());

        return $user;
    }
}
