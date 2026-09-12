<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Activity;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentType;
use App\State\Util\AbstractPersistProcessor;
use App\Util\EntityMap;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @template-extends AbstractPersistProcessor<Activity>
 */
class ActivityResetProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private EntityManagerInterface $em
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Activity $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): Activity {
        // @phpstan-ignore nullsafe.neverNull
        if (!isset($data->category?->rootContentNode)) {
            throw new \UnexpectedValueException('Property rootContentNode of provided category is null. Object of type '.ColumnLayout::class.' expected.');
        }
        if (!$data->category->rootContentNode instanceof ColumnLayout) {
            throw new \UnexpectedValueException('Property rootContentNode of provided category is of wrong type. Object of type '.ColumnLayout::class.' expected.');
        }

        // Delete the old content
        $this->em->remove($data->rootContentNode);

        // Copy content from the category
        $targetCamp = $data->category->camp;
        $rootContentNodePrototype = $data->category->rootContentNode;

        $rootContentNode = new ColumnLayout();
        $rootContentNode->contentType = $this->em
            ->getRepository(ContentType::class)
            ->findOneBy(['name' => 'ColumnLayout'])
        ;
        $data->setRootContentNode($rootContentNode);

        $entityMap = new EntityMap($targetCamp);
        $rootContentNode->copyFromPrototype($rootContentNodePrototype, $entityMap);

        return $data;
    }

    private function deleteContent(Activity $activity): void {

    }
}
