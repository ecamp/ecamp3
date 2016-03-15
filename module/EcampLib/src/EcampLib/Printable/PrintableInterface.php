<?php

namespace EcampLib\Printable;

use Zend\View\Model\ViewModel;

interface PrintableInterface
{
    /** @return ViewModel */
    public function create(array $item);
}
