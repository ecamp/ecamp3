<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BaseEntity;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\MaterialList;
use App\Entity\User;
use App\Repository\CampRepository;
use App\Service\Hitobito\ClientProvider;
use App\Service\Hitobito\EventAccessChecker;
use App\Service\Hitobito\HitobitoProvider;
use App\State\Util\AbstractPersistProcessor;
use App\Util\EntityMap;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * @template-extends AbstractPersistProcessor<Camp>
 */
class CampCreateProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
        private readonly ClientProvider $hitobitoClientProvider,
        private readonly EventAccessChecker $hitobitoEventAccessChecker,
        private readonly CampRepository $campRepository
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Camp $data
     */
    #[\Override]
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): BaseEntity {
        // When linking the camp to a Hitobito event, check that user has permissions to do so and that no existing linked camp exists
        if (null !== $data->hitobitoProvider && null !== $data->hitobitoEventId) {
            $this->checkHitobitoEvent($data->hitobitoProvider, $data->hitobitoEventId);
        }

        /** @var User $user */
        $user = $this->security->getUser();
        $data->creator = $user;
        $data->owner = $user;
        $data->isPublic = $data->isShared || $data->isPrototype;

        // copy from prototype, if given
        if (null !== $data->campPrototype) {
            $entityMap = new EntityMap($data);
            $data->copyFromPrototype($data->campPrototype, $entityMap);
        }

        foreach ($data->periods as $period) {
            PeriodPersistProcessor::addMissingDays($period);
        }

        return $data;
    }

    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        /** @var Camp $data */
        /** @var User $user */
        $user = $this->security->getUser();
        $collaboration = new CampCollaboration();
        $collaboration->user = $user;
        $collaboration->role = CampCollaboration::ROLE_MANAGER;
        $collaboration->status = CampCollaboration::STATUS_ESTABLISHED;
        $data->addCampCollaboration($collaboration);
        $this->em->persist($collaboration);

        $materialList = new MaterialList();
        $materialList->campCollaboration = $collaboration;
        $data->addMaterialList($materialList);
        $this->em->persist($materialList);

        $this->em->flush();
    }

    /**
     * Checks that
     * - the current user is a leader of the given Hitobito event
     * - no camp exists that is linked to the specified event.
     */
    private function checkHitobitoEvent(HitobitoProvider $provider, string $eventId): void {
        $client = $this->hitobitoClientProvider->getClientForCurrentUser($provider);
        $this->hitobitoEventAccessChecker->checkAccess($provider, $client, $eventId);

        $existingCamp = $this->campRepository->findOneBy(['hitobitoProvider' => $provider, 'hitobitoEventId' => $eventId]);
        if (null !== $existingCamp) {
            throw new ConflictHttpException("A camp already exists for the event \"{$eventId}\"");
        }
    }
}
