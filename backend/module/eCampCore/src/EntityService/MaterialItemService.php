<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\MaterialItem;
use eCamp\Core\Hydrator\MaterialItemHydrator;
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
