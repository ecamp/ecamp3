<?php

namespace App\State\ContentNode;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ContentNode\SingleText;
use App\InputFilter\CleanHTMLFilter;

class SingleTextPersistProcessor extends ContentNodePersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private CleanHTMLFilter $cleanHTMLFilter,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param SingleText $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): SingleText {
        $data = parent::onBefore($data, $operation, $uriVariables, $context);

        $data->data = $this->cleanHTMLFilter->applyTo($data->data, 'html');

        return $data;
    }
}
