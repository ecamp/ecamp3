<?php

namespace App\DataPersister;

use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\BaseEntity;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\MaterialList;
use App\Entity\User;
use App\Util\EntityMap;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CampDataPersister extends AbstractDataPersister {
    public function __construct(
        DataPersisterObservable $dataPersisterObservable,
        private Security $security,
        private EntityManagerInterface $em,
    ) {
        parent::__construct(
            Camp::class,
            $dataPersisterObservable,
        );
    }

    /**
     * @param Camp $data
     */
    public function beforeCreate($data): BaseEntity {
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
            PeriodDataPersister::updateDaysAndScheduleEntries($period);
        }

        return $data;
    }

    public function afterCreate($data): void {
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
     * @param Camp $data
     */
    public function beforeRemove($data): ?BaseEntity {
        // Deleting rootContentNode would normally be done automatically with orphanRemoval:true
        // However, this currently runs into an error due to https://github.com/doctrine-extensions/DoctrineExtensions/issues/2510

        foreach ($data->activities->getIterator() as $activity) {
            $this->em->refresh($activity->rootContentNode);
            $this->em->remove($activity->rootContentNode);
        }

        foreach ($data->categories->getIterator() as $category) {
            $this->em->refresh($category->rootContentNode);
            $this->em->remove($category->rootContentNode);
        }

        return null;
    }
}
