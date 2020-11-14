<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\MaterialItem;
use eCamp\Core\Entity\MaterialList;
use eCamp\Core\Entity\Period;
use eCamp\Core\Hydrator\MaterialItemHydrator;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class MaterialItemService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            MaterialItem::class,
            MaterialItemHydrator::class,
            $authenticationService
        );
    }

    /**
     * @param $data
     *
     * @return MaterialItem
     */
    protected function createEntity($data) {
        /** @var MaterialItem $materialItem */
        $materialItem = parent::createEntity($data);

        /** @var MaterialList $materialList */
        $materialList = $this->findRelatedEntity(MaterialList::class, $data, 'materialListId');
        $materialList->addMaterialItem($materialItem);
        $camp = $materialList->getCamp();

        if (isset($data->periodId)) {
            /** @var Period $period */
            $period = $this->findRelatedEntity(Period::class, $data, 'periodId');
            if ($period->getCamp()->getId() !== $camp->getId()) {
                throw (new EntityValidationException())->setMessages([
                    'materialListId' => ['campMismatch' => 'Provided materiallist is not part of the same camp as provided period'],
                    'periodId' => ['campMismatch' => 'Provided materiallist is not part of the same camp as provided period'],
                ]);
            }
            $period->addMaterialItem($materialItem);
        }

        if (isset($data->activityContentId)) {
            /** @var ActivityContent $activityContent */
            $activityContent = $this->findRelatedEntity(ActivityContent::class, $data, 'activityContentId');
            if ($activityContent->getCamp()->getId() !== $camp->getId()) {
                throw (new EntityValidationException())->setMessages([
                    'materialListId' => ['campMismatch' => 'Provided materiallist is not part of the same camp as provided activityContent'],
                    'activityContentId' => ['campMismatch' => 'Provided materiallist is not part of the same camp as provided activityContent'],
                ]);
            }
            $activityContent->addMaterialItem($materialItem);
        }

        return $materialItem;
    }

    protected function patchEntity(BaseEntity $entity, $data) {
        /** @var MaterialItem $materialItem */
        $materialItem = parent::patchEntity($entity, $data);
        $camp = $materialItem->getCamp();

        if (isset($data->materialListId)) {
            /** @var MaterialList $materialList */
            $materialList = $this->findRelatedEntity(MaterialList::class, $data, 'materialListId');
            if ($camp->getId() !== $materialList->getCamp()->getId()) {
                throw (new EntityValidationException())->setMessages([
                    'materialListId' => ['campMismatch' => 'Provided materiallist is not part of the same camp'],
                ]);
            }
            $materialList->addMaterialItem($materialItem);
        }

        if (isset($data->periodId)) {
            /** @var Period $period */
            $period = $this->findRelatedEntity(Period::class, $data, 'periodId');
            if ($camp->getId() !== $period->getCamp()->getId()) {
                throw (new EntityValidationException())->setMessages([
                    'periodId' => ['campMismatch' => 'Provided period is not part of the same camp'],
                ]);
            }
            $period->addMaterialItem($materialItem);
        }

        if (isset($data->activityContentId)) {
            /** @var ActivityContent $activityContent */
            $activityContent = $this->findRelatedEntity(ActivityContent::class, $data, 'activityContentId');
            if ($camp->getId() !== $activityContent->getCamp()->getId()) {
                throw (new EntityValidationException())->setMessages([
                    'activityContentId' => ['campMismatch' => 'Provided activityContent is not part of the same camp'],
                ]);
            }
            $activityContent->addMaterialItem($materialItem);
        }
    }

    protected function fetchAllQueryBuilder($params = []) {
        /** @var QueryBuilder $q */
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.materialList', 'ml');
        $q->andWhere($this->createFilter($q, Camp::class, 'ml', 'camp'));

        if (isset($params['campId'])) {
            $q->andWhere('ml.camp = :campId');
            $q->setParameter('campId', $params['campId']);
        }
        if (isset($params['materialListId'])) {
            $q->andWhere('row.materialList = :materialListId');
            $q->setParameter('materialListId', $params['materialListId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.materialList', 'ml');
        $q->andWhere($this->createFilter($q, Camp::class, 'ml', 'camp'));

        return $q;
    }
}
