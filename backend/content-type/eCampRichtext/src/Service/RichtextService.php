<?php

namespace eCamp\ContentType\Richtext\Service;

use eCamp\ContentType\Richtext\Entity\Richtext;
use eCamp\ContentType\Richtext\Hydrator\RichtextHydrator;
use eCamp\Core\ContentType\BaseContentTypeService;
use eCamp\Lib\Service\ServiceUtils;
use HTMLPurifier;
use Laminas\Authentication\AuthenticationService;

class RichtextService extends BaseContentTypeService {
    private $htmlPurifier;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        HTMLPurifier $htmlPurifier
    ) {
        parent::__construct(
            $serviceUtils,
            Richtext::class,
            RichtextHydrator::class,
            $authenticationService
        );

        $this->htmlPurifier = $htmlPurifier;
    }

    protected function prepareData($data) {
        if (isset($data->text)) {
            $data->text = $this->htmlPurifier->purify($data->text);
        }

        return $data;
    }
}
