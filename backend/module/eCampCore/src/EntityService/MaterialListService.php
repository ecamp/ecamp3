<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\MaterialList;
use eCamp\Lib\Service\ServiceUtils;
use eCamp\Core\Hydrator\MaterialListHydrator;
use Laminas\Authentication\AuthenticationService;

class MaterialListService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            MaterialList::class,
            MaterialListHydrator::class,
            $authenticationService
        );
    }
    
    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        if (isset($params['campId'])) {
            $q->andWhere('row.camp = :campId');
            $q->setParameter('campId', $params['campId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        return $q;
    }
    
}
