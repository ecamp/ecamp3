<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Category;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentType;
use App\State\Util\AbstractPersistProcessor;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @template-extends AbstractPersistProcessor<Category>
 */
class CategoryCreateProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private EntityManagerInterface $em,
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Category $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): Category {
        // TODO implement actual prototype cloning and strategy classes, this is just a dummy implementation to
        //      fill the non-nullable field for Doctrine
        $rootContentNode = new ColumnLayout();
        $rootContentNode->contentType = $this->em
            ->getRepository(ContentType::class)
            ->findOneBy(['name' => 'ColumnLayout'])
        ;
        $rootContentNode->data = ['columns' => [['slot' => '1', 'width' => 12]]];
        /*
         * Set the timestampable properties here by hand, because only in production
         * this does not work.
         * Remove this again as soon as https://github.com/ecamp/ecamp3/issues/3662 is really fixed.
         */
        $rootContentNode->updateCreateTime();
        $rootContentNode->updateUpdateTime();

        $data->setRootContentNode($rootContentNode);

        return $data;
    }
}
