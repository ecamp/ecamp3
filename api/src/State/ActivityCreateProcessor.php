<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Activity;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentType;
use App\Util\EntityMap;
use Doctrine\ORM\EntityManagerInterface;

final class ActivityCreateProcessor implements ProcessorInterface {
    public function __construct(
        private ProcessorInterface $decorated,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @param Activity $data
     *
     * @return Activity
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []) {
        $data = $this->beforeCreate($data);

        return $this->decorated->process($data, $operation, $uriVariables, $context);
    }

    /**
     * @param Activity $data
     */
    public function beforeCreate($data): Activity {
        $data->camp = $data->category?->camp;

        if (!isset($data->category?->rootContentNode)) {
            throw new \UnexpectedValueException('Property rootContentNode of provided category is null. Object of type '.ColumnLayout::class.' expected.');
        }

        if (!is_a($data->category->rootContentNode, ColumnLayout::class)) {
            throw new \UnexpectedValueException('Property rootContentNode of provided category is of wrong type. Object of type '.ColumnLayout::class.' expected.');
        }

        $rootContentNode = new ColumnLayout();
        $rootContentNode->contentType = $this->em
            ->getRepository(ContentType::class)
            ->findOneBy(['name' => 'ColumnLayout'])
        ;
        $data->setRootContentNode($rootContentNode);

        // deep copy from category root node
        $entityMap = new EntityMap();
        $rootContentNode->copyFromPrototype($data->category->rootContentNode, $entityMap);

        return $data;
    }
}
