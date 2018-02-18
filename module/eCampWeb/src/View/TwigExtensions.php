<?php

namespace eCamp\Web\View;

use eCamp\Core\Service\CampService;
use eCamp\Web\View\Helper\CampInfo;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Twig\TwigFunction;
use ZendTwig\Extension\Extension;

class TwigExtensions extends Extension
{
    public function getFunctions() {
        return [
            'campInfo' => new TwigFunction('campInfo', [$this, 'campInfo'])
        ];
    }

    /**
     * @param $campId
     * @return CampInfo
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function campInfo($campId) {
        /** @var CampService $campService */
        $campService = $this->getServiceManager()->get(CampService::class);

        return new CampInfo($campService, $campId);
    }

}
