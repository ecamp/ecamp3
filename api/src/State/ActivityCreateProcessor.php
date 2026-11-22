<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Activity;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentType;
use App\Service\Image\CampImageCopyService;
use App\State\Util\AbstractPersistProcessor;
use App\Util\EntityMap;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @template-extends AbstractPersistProcessor<Activity>
 */
class ActivityCreateProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private readonly EntityManagerInterface $em,
        private readonly CampImageCopyService $campImageCopyService,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Activity $data
     */
    #[\Override]
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): Activity {
        // @phpstan-ignore nullsafe.neverNull
        if (!isset($data->category?->rootContentNode)) {
            throw new \UnexpectedValueException('Property rootContentNode of provided category is null. Object of type '.ColumnLayout::class.' expected.');
        }
        if (!$data->category->rootContentNode instanceof ColumnLayout) {
            throw new \UnexpectedValueException('Property rootContentNode of provided category is of wrong type. Object of type '.ColumnLayout::class.' expected.');
        }

        $targetCamp = $data->category->camp;
        $data->camp = $data->category->camp;
        $rootContentNodePrototype = $data->category->rootContentNode;

        if (null !== $data->copyActivitySource) {
            // CopyActivity Source is set -> copy it's content (rootContentNode)
            $rootContentNodePrototype = $data->copyActivitySource->rootContentNode;
        }

        $rootContentNode = new ColumnLayout();
        $rootContentNode->contentType = $this->em
            ->getRepository(ContentType::class)
            ->findOneBy(['name' => 'ColumnLayout'])
        ;
        $data->setRootContentNode($rootContentNode);

        // deep copy from category root node
        $entityMap = new EntityMap($targetCamp);
        $rootContentNode->copyFromPrototype($rootContentNodePrototype, $entityMap);
        $this->campImageCopyService->copy($entityMap);

        return $data;
    }
}
