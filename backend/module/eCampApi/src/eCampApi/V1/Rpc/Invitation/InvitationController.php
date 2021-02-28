<?php

namespace eCampApi\V1\Rpc\Invitation;

use Doctrine\ORM\NonUniqueResultException;
use eCamp\Core\Hydrator\InvitationHydrator;
use eCamp\Core\Service\Invitation;
use eCamp\Core\Service\InvitationService;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\ApiTools\Hal\View\HalJsonModel;
use Laminas\ApiTools\Rpc\RpcController;
use Laminas\Authentication\AuthenticationService;

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
            ],
        ]);
        $data['find'] = Link::factory([
            'rel' => 'find',
            'route' => [
                'name' => 'e-camp-api.rpc.invitation.find',
                'params' => [
                    'inviteKey' => 'add_inviteKey_here',
                ],
            ],
        ]);
        $data['accept'] = Link::factory([
            'rel' => 'accept',
            'route' => [
                'name' => 'e-camp-api.rpc.invitation.accept',
                'params' => [
                    'inviteKey' => 'add_inviteKey_here',
                ],
            ],
        ]);
        $data['reject'] = Link::factory([
            'rel' => 'reject',
            'route' => [
                'name' => 'e-camp-api.rpc.invitation.reject',
                'params' => [
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
     * @throws NoAccessException
     * @throws NonUniqueResultException
     */
    public function find($inviteKey): HalJsonModel {
        $invitation = $this->invitationService->findInvitation($inviteKey);
        if (null == $invitation) {
            throw new EntityNotFoundException('Not Found', 404);
        }

        return $this->toResponse($invitation);
    }

    /**
     * @param $inviteKey
     *
     * @throws NoAccessException
     * @throws EntityValidationException
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    public function accept($inviteKey): HalJsonModel {
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
        } catch (EntityValidationException $e) {
            $entityValidationException = new EntityValidationException('Failed to update Invitation', 422);
            $entityValidationException->setMessages($e->getMessages());

            throw $entityValidationException;
        }
    }

    /**
     * @param $inviteKey
     *
     * @throws EntityNotFoundException
     */
    public function reject($inviteKey): HalJsonModel {
        try {
            return $this->toResponse($this->invitationService->rejectInvitation($inviteKey));
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException('Not Found', 404);
        }
    }

    private function toResponse(Invitation $invitation): HalJsonModel {
        $hydrator = $this->serviceUtils->getHydrator(InvitationHydrator::class);

        $data = $hydrator->extract($invitation);
        $data['self'] = Link::factory([
            'rel' => 'self',
            'route' => [
                'name' => 'e-camp-api.rpc.invitation.find',
            ],
        ]);
        $json = new HalJsonModel();
        $payload = new Entity($data);

        $json->setPayload($payload);

        return $json;
    }
}
