<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BaseEntity;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\MaterialList;
use App\Entity\User;
use App\State\Util\AbstractPersistProcessor;
use App\Util\EntityMap;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CampCreateProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private Security $security,
        private EntityManagerInterface $em,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Camp $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): BaseEntity {
        /** @var User $user */
        $user = $this->security->getUser();
        $data->creator = $user;
        $data->owner = $user;

        // copy from prototype, if given
        if (isset($data->campPrototype)) {
            $entityMap = new EntityMap();
            $data->copyFromPrototype($data->campPrototype, $entityMap);
        }

        foreach ($data->periods as $period) {
            PeriodPersistProcessor::updateDaysAndScheduleEntries($period);
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
}
