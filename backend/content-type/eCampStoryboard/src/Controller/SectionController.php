<?php

namespace eCamp\ContentType\Storyboard\Controller;

use Laminas\Stdlib\Parameters;
use eCamp\Core\ContentType\ContentTypeRestController;

class SectionController extends ContentTypeRestController {


     /**
     * Injects activityContentId query param
     *
     * @param array $data
     * @return array|ApiProblem
     */
    public function patchList($data)
    {
        // logic copied from RestControllerFactory.php / collection_query_whitelist / getList.pre
        $resource   = $this->getResource();
        $request = $this->getRequest();
        $query  = $request->getQuery();
        $params = new Parameters([]);
        $params->set('activityContentId', $query['activityContentId']);
        $resource->setQueryParams($params);

        // main call to  patchList 
        $collection = parent::patchList($data);

        // logic copied from RestControllerFactory.php / collection_query_whitelist / getList.post
        $params = $resource->getQueryParams()->getArrayCopy();
        $collection->setCollectionRouteOptions([
            'query' => $params,
        ]);
        $links = $collection->getLinks();
        $self  = $links->get('self');
        $options = $self->getRouteOptions();
        $self->setRouteOptions(array_merge($options, [
            'query' => $params,
        ]));

        return $collection;
    }
}
