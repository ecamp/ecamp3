<?php

namespace eCampApi\V1\Rpc\Printer;

use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use eCamp\Lib\Hydrator\Util;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\ApiProblem\View\ApiProblemModel;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\ApiTools\Hal\View\HalJsonModel;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Mvc\Controller\AbstractActionController;

use Enqueue\AmqpBunny\AmqpConnectionFactory;
use Interop\Amqp\AmqpTopic;
use Interop\Amqp\AmqpQueue;
use Interop\Amqp\Impl\AmqpBind;
use Enqueue\ConnectionFactoryFactory;

/**
 * PrinterController
 */
class PrinterController extends AbstractActionController {
    /** @var AuthenticationService */
    private $authenticationService;

    /** @var UserService */
    private $userService;

    public function __construct(
        AuthenticationService $authenticationService,
        UserService $userService
    ) {
        $this->authenticationService = $authenticationService;
        $this->userService = $userService;
    }

    public function indexAction() {
        /* make sure user is logged in */
        if (! $this->authenticationService->hasIdentity())
            return new ApiProblemModel(new ApiProblem(401, null));
    
        $userId = $this->authenticationService->getIdentity();
        /** @var User $user */
        $user = $this->userService->fetch($userId);
        
        // connect to AMQP broker at example.com
        $factory = new AmqpConnectionFactory([
            'host' => 'rabbitmq',
            'port' => 5672,
            'vhost' => '/',
            'user' => 'guest',
            'pass' => 'guest',
            'persisted' => false,
        ]);
        $context = $factory->createContext();

        $printTopic = $context->createTopic('print');
        $printTopic->setType(AmqpTopic::TYPE_FANOUT);
        $context->declareTopic($printTopic);
        
        $weasyQueue = $context->createQueue('printer-weasy');
        $weasyQueue->addFlag(AmqpQueue::FLAG_DURABLE);
        $context->declareQueue($weasyQueue);

        $puppeteerQueue = $context->createQueue('printer-puppeteer');
        $puppeteerQueue->addFlag(AmqpQueue::FLAG_DURABLE);
        $context->declareQueue($puppeteerQueue);

        $context->bind(new AmqpBind($printTopic, $weasyQueue));
        $context->bind(new AmqpBind($printTopic, $puppeteerQueue));

        $messageArray = [
            'campId' => '4f885733', 
            'filename' => uniqid(),
            'PHPSESSID' => session_id()
        ];
        
        $message = $context->createMessage(json_encode($messageArray));
        $context->createProducer()->send($printTopic, $message);


        $data = [];

        $data['self'] = Link::factory([
            'rel' => 'self',
            'route' => 'e-camp-api.rpc.printer',
        ]);

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }


   
}
