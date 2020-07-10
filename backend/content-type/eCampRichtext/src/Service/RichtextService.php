<?php

namespace eCamp\ContentType\Richtext\Service;

use eCamp\ContentType\Richtext\Entity\Richtext;
use eCamp\ContentType\Richtext\Hydrator\RichtextHydrator;
use eCamp\Core\ContentType\BaseContentTypeService;
use eCamp\Core\Entity\Camp;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class RichtextService extends BaseContentTypeService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            Richtext::class,
            RichtextHydrator::class,
            $authenticationService
        );
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.activityContent', 'ac');
        $q->join('ac.activity', 'a');
        $q->andWhere($this->createFilter($q, Camp::class, 'a', 'camp'));

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.activityContent', 'ac');
        $q->join('ac.activity', 'a');
        $q->andWhere($this->createFilter($q, Camp::class, 'a', 'camp'));

        return $q;
    }
}
