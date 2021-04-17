<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\MaterialList;
use eCamp\Core\Hydrator\MaterialListHydrator;
use eCamp\Lib\Service\ServiceUtils;
use eCampApi\V1\Rest\MaterialList\MaterialListCollection;
use Laminas\Authentication\AuthenticationService;

class MaterialListService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            MaterialList::class,
            MaterialListCollection::class,
            MaterialListHydrator::class,
            $authenticationService
        );
    }

    public function createFromPrototype(Camp $camp, MaterialList $prototype): MaterialList {
        /** @var MaterialList $materialList */
        $materialList = $this->create((object) [
            'campId' => $camp->getId(),
            'name' => $prototype->getName(),
        ]);
        $materialList->setMaterialListPrototypeId($prototype->getId());

        return $materialList;
    }

    /**
     * @param $data
     */
    protected function createEntity($data): MaterialList {
        /** @var MaterialList $materialList */
        $materialList = parent::createEntity($data);

        /** @var Camp $camp */
        $camp = $this->findRelatedEntity(Camp::class, $data, 'campId');
        $camp->addMaterialList($materialList);

        return $materialList;
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        if (isset($params['campId'])) {
            $q->andWhere('row.camp = :campId');
            $q->setParameter('campId', $params['campId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id): QueryBuilder {
        $q = parent::fetchQueryBuilder($id);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        return $q;
    }
}
