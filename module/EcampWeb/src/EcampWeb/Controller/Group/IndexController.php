<?php

namespace EcampWeb\Controller\Group;

use EcampWeb\Element\ApiCollectionPaginator;

class IndexController
    extends BaseController
{

    public function indexAction()
    {
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headScript()->appendFile('/js/ng-app/paginator.js');

        $subgroupResourceUrl = $this->url()->fromRoute(
            'api/groups/groups', array('group' => $this->getGroup()->getId()));
        $campsResourceUrl = $this->url()->fromRoute(
            'api/groups/camps', array('group' => $this->getGroup()->getId()));

        $subgroupPaginator = new ApiCollectionPaginator($subgroupResourceUrl);
        $subgroupPaginator->setItemsPerPage(10);

        $campsPaginator = new ApiCollectionPaginator($campsResourceUrl);
        $campsPaginator->setItemsPerPage(10);

        return array(
            'subgroupPaginator' => $subgroupPaginator,
            'campsPaginator' => $campsPaginator
        );
    }

    public function membersAction()
    {
        return array();
    }

}
