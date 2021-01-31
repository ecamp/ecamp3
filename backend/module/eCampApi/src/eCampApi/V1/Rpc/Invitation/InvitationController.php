<?php

namespace eCampApi\V1\Rpc\Invitation;

use Doctrine\ORM\NonUniqueResultException;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Hydrator\CampCollaborationHydrator;
use eCamp\Core\Service\InvitationService;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Hydrator\Resolver\BaseResolver;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\ApiTools\Hal\View\HalJsonModel;
use Laminas\ApiTools\Rpc\RpcController;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Request;

class InvitationController extends RpcController {
    private AuthenticationService $authenticationService;
    private InvitationService $invitationService;
    private ServiceUtils $serviceUtils;

    public function __construct(
        AuthenticationService $authenticationService,
        InvitationService $invitationService,
        ServiceUtils $serviceUtils
    ) {
        $this->authenticationService = $authenticationService;
        $this->invitationService = $invitationService;
        $this->serviceUtils = $serviceUtils;
    }

    public function index(): HalJsonModel {
        $data = [];
        $data['title'] = 'Invitations';
        $data['self'] = Link::factory([
            'rel' => 'self',
            'route' => [
                'name' => 'e-camp-api.rpc.invitation',
                'params' => [
                    'action' => 'index',
                ],
            ],
        ]);
        $data['find'] = Link::factory([
            'rel' => 'find',
            'route' => [
                'name' => 'e-camp-api.rpc.invitation',
                'params' => [
                    'action' => 'find',
                    'inviteKey' => 'add_inviteKey_here',
                ],
            ],
        ]);
        $data['accept'] = Link::factory([
            'rel' => 'accept',
            'route' => [
                'name' => 'e-camp-api.rpc.invitation',
                'params' => [
                    'action' => 'accept',
                    'inviteKey' => 'add_inviteKey_here',
                ],
            ],
        ]);
        $data['reject'] = Link::factory([
            'rel' => 'reject',
            'route' => [
                'name' => 'e-camp-api.rpc.invitation',
                'params' => [
                    'action' => 'reject',
                    'inviteKey' => 'add_inviteKey_here',
                ],
            ],
        ]);
        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }

    /**
     * @param $inviteKey
     *
     * @throws EntityNotFoundException
     */
    public function find($inviteKey): HalJsonModel {
        $campCollaboration = $this->invitationService->findInvitation($inviteKey);
        if (null == $campCollaboration) {
            throw new EntityNotFoundException('Not Found', 404);
        }

        return $this->toResponse($campCollaboration);
    }

    /**
     * @param $inviteKey
     *
     * @throws EntityNotFoundException
     * @throws NoAccessException
     */
    public function accept($inviteKey): HalJsonModel {
        /** @var Request $request */
        $request = $this->getRequest();

        if (!$request->isPost()) {
            throw new \Exception('Bad Request', 400);
        }
        if (!$this->authenticationService->hasIdentity()) {
            throw new EntityNotFoundException('Not Authorized', 401);
        }
        $userId = $this->authenticationService->getIdentity();

        try {
            return $this->toResponse($this->invitationService->acceptInvitation($inviteKey, $userId));
        } catch (NonUniqueResultException $e) {
            throw new \Exception('Error getting CampCollaboration', 500);
        } catch (NoAccessException $e) {
            throw new NoAccessException('You cannot fetch your own User', 401);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException('Not Found', 404);
        }
    }

    /**
     * @param $inviteKey
     *
     * @throws EntityNotFoundException
     */
    public function reject($inviteKey): HalJsonModel {
        /** @var Request $request */
        $request = $this->getRequest();
        if (!$request->isPost()) {
            throw new \Exception('Bad Request', 400);
        }

        try {
            return $this->toResponse($this->invitationService->rejectInvitation($inviteKey));
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException('Not Found', 404);
        }
    }

    private function toResponse(CampCollaboration $campCollaboration): HalJsonModel {
        $hydrator = $this->serviceUtils->getHydrator(CampCollaborationHydrator::class);

        $data = $hydrator->extract($campCollaboration);
        if (method_exists($hydrator, 'HydrateInfo')) {
            $hydrateInfo = call_user_func([$hydrator, 'HydrateInfo']);
            foreach ($hydrateInfo as $key => $resolver) {
                /** @var BaseResolver $resolver */
                $value = $resolver->resolve($campCollaboration);
                $data[$key] = $value;
            }
        }
        $data['self'] = Link::factory([
            'rel' => 'self',
            'route' => [
                'name' => 'e-camp-api.rest.doctrine.camp-collaboration',
                'params' => [
                    'campCollaborationId' => $campCollaboration->getId(),
                ],
            ],
        ]);
        $json = new HalJsonModel();
        $payload = new Entity($data);

        $json->setPayload($payload);

        return $json;
    }
}
