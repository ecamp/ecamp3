<?php

namespace EcampWeb\Element;

class ApiCollectionPaginator
{

    private $resourceUrl;
    private $currentPageNumber = 1;
    private $itemsPerPage = null;

    public function __construct($resourceUrl)
    {
        $this->resourceUrl = $resourceUrl;
    }

    public function setCurrentPageNumber($currentPageNumber)
    {
        $this->currentPageNumber = $currentPageNumber;
    }

    public function getCurrentPageNumber()
    {
        return $this->currentPageNumber;
    }

    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    public function getNgController()
    {
        $params = array("'page': " . $this->currentPageNumber);
        if (isset($this->itemsPerPage)) {
            $params[] = "'limit': " . $this->itemsPerPage;
        }
        $params = "{ " . implode(", ", $params) . " }";

        $ctr  = "ng-cloak ng-controller=\"Paginator\" ";
        $ctr .= "ng-init=\"init('" . $this->resourceUrl . "', " . $params;
        $ctr .= ")\"";

        return $ctr;
    }

    public function getCount()
    {
        return "{{ getCount() }}";
    }
}
