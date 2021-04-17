<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\MaterialItem;
use eCamp\Core\Entity\MaterialList;
use eCamp\Core\Entity\Period;
use eCamp\Core\Hydrator\MaterialItemHydrator;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\Lib\Service\ServiceUtils;
use eCampApi\V1\Rest\MaterialItem\MaterialItemCollection;
use Laminas\Authentication\AuthenticationService;

class MaterialItemService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            MaterialItem::class,
            MaterialItemCollection::class,
            MaterialItemHydrator::class,
            $authenticationService
        );
    }

    /**
     * @param $data
     */
    protected function createEntity($data): MaterialItem {
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

        if (isset($data->contentNodeId)) {
            /** @var ContentNode $contentNode */
            $contentNode = $this->findRelatedEntity(ContentNode::class, $data, 'contentNodeId');
            if ($contentNode->getCamp()->getId() !== $camp->getId()) {
                throw (new EntityValidationException())->setMessages([
                    'materialListId' => ['campMismatch' => 'Provided materiallist is not part of the same camp as provided contentNode'],
                    'contentNodeId' => ['campMismatch' => 'Provided materiallist is not part of the same camp as provided contentNode'],
                ]);
            }
            $materialItem->setContentNode($contentNode);
        }

        return $materialItem;
    }

    protected function patchEntity(BaseEntity $entity, $data): MaterialItem {
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

        if (isset($data->contentNodeId)) {
            /** @var ContentNode $contentNode */
            $contentNode = $this->findRelatedEntity(ContentNode::class, $data, 'contentNodeId');
            if ($camp->getId() !== $contentNode->getCamp()->getId()) {
                throw (new EntityValidationException())->setMessages([
                    'contentNodeId' => ['campMismatch' => 'Provided contentNode is not part of the same camp'],
                ]);
            }
            $materialItem->setContentNode($contentNode);
        }

        return $materialItem;
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
        /** @var QueryBuilder $q */
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.materialList', 'ml');
        $q->andWhere($this->createFilter($q, Camp::class, 'ml', 'camp'));

        if (isset($params['campId'])) {
            $q->andWhere('ml.camp = :campId');
            $q->setParameter('campId', $params['campId']);
        }
        if (isset($params['periodId'])) {
            $q->andWhere('row.period = :periodId');
            $q->setParameter('periodId', $params['periodId']);
        }
        if (isset($params['materialListId'])) {
            $q->andWhere('row.materialList = :materialListId');
            $q->setParameter('materialListId', $params['materialListId']);
        }
        if (isset($params['contentNodeId'])) {
            $q->andWhere('row.contentNode = :contentNodeId');
            $q->setParameter('contentNodeId', $params['contentNodeId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id): QueryBuilder {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.materialList', 'ml');
        $q->andWhere($this->createFilter($q, Camp::class, 'ml', 'camp'));

        return $q;
    }

    protected function validateEntity(BaseEntity $entity): void {
        /** @var MaterialItem $materialItem */
        $materialItem = $entity;

        if (null == $materialItem->getContentNode() && null == $materialItem->getPeriod()) {
            $ex = new EntityValidationException();
            $ex->setMessages([
                'periodId' => ['required' => 'periodId or contentNodeId is required'],
                'contentNodeId' => ['required' => 'periodId or contentNodeId is required'],
            ]);

            throw $ex;
        }
    }
}
