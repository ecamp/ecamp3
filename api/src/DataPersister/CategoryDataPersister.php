<?php

namespace App\DataPersister;

use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\Category;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentType;
use Doctrine\ORM\EntityManagerInterface;

class CategoryDataPersister extends AbstractDataPersister {
    public function __construct(
        DataPersisterObservable $dataPersisterObservable,
        private EntityManagerInterface $em,
    ) {
        parent::__construct(
            Category::class,
            $dataPersisterObservable,
        );
    }

    /**
     * @param Category $data
     */
    public function beforeCreate($data): Category {
        // TODO implement actual prototype cloning and strategy classes, this is just a dummy implementation to
        //      fill the non-nullable field for Doctrine
        $rootContentNode = new ColumnLayout();
        $rootContentNode->contentType = $this->em
            ->getRepository(ContentType::class)
            ->findOneBy(['name' => 'ColumnLayout'])
        ;
        $rootContentNode->data = ['columns' => [['slot' => '1', 'width' => 12]]];
        $data->setRootContentNode($rootContentNode);

        return $data;
    }

    /**
     * @param Category $data
     */
    public function beforeRemove($data): ?Category {
        // Deleting rootContentNode would normally be done automatically with orphanRemoval:true
        // However, this currently runs into an error due to https://github.com/doctrine-extensions/DoctrineExtensions/issues/2510

        $this->em->remove($data->rootContentNode);

        return null;
    }
}
