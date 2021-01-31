<?php

namespace eCampApi\V1\Rpc\Printer;

use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use eCamp\Lib\Amqp\AmqpService;
use eCampApi\V1\Rpc\ApiController;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\ApiProblem\View\ApiProblemModel;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\ApiTools\Hal\View\HalJsonModel;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\View\Model\ViewModel;

/**
 * PrinterController.
 */
class PrinterController extends ApiController {
    private AuthenticationService $authenticationService;
    private UserService $userService;
    private AmqpService $amqpService;

    public function __construct(
        AuthenticationService $authenticationService,
        UserService $userService,
        AmqpService $amqpService
    ) {
        $this->authenticationService = $authenticationService;
        $this->userService = $userService;
        $this->amqpService = $amqpService;
    }

    public function indexAction(): ViewModel {
        // make sure user is logged in
        if (!$this->authenticationService->hasIdentity()) {
            return new ApiProblemModel(new ApiProblem(401, null));
        }
        /** @var Request $request */
        $request = $this->getRequest();
        $content = $request->getContent();

        $data = (null != $content) ? Json::decode($content) : [];

        if (!isset($data->campId)) {
            return new ApiProblemModel(new ApiProblem(400, 'No campId provided'));
        }

        // TODO: check if user has permission to print given camp

        $userId = $this->authenticationService->getIdentity();
        /** @var User $user */
        $user = $this->userService->fetch($userId);

        $printTopic = $this->amqpService->createTopic('print');
        $this->amqpService->createQueue('printer-weasy', $printTopic);
        $this->amqpService->createQueue('printer-puppeteer', $printTopic);

        $filename = bin2hex(random_bytes(16));

        $messageArray = [
            'campId' => $data->campId,
            'filename' => $filename,
            'PHPSESSID' => session_id(),
            'config' => $data->config,
        ];

        $this->amqpService->sendAsJson($printTopic, $messageArray);

        $data = [];

        $data['self'] = Link::factory([
            'rel' => 'self',
            'route' => 'e-camp-api.rpc.printer',
        ]);

        $data['filename'] = $filename;

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }
}
