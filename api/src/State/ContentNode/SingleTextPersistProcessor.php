<?php

namespace App\State\ContentNode;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ContentNode\SingleText;
use App\InputFilter\CleanHTMLFilter;
use App\Service\Image\ImageReferenceValidator;

/**
 * @template-extends ContentNodePersistProcessor<SingleText>
 */
class SingleTextPersistProcessor extends ContentNodePersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly CleanHTMLFilter $cleanHTMLFilter,
        private readonly ImageReferenceValidator $imageReferenceValidator,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param SingleText $data
     */
    #[\Override]
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): SingleText {
        $data = parent::onBefore($data, $operation, $uriVariables, $context);

        $data->data = $this->cleanHTMLFilter->applyTo($data->data, 'html');
        $this->imageReferenceValidator->validate($data->data['html'], $data->getCamp());

        return $data;
    }
}
