<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\Image\ImageLifecycleLock;
use Doctrine\ORM\EntityManagerInterface;

/** @implements ProcessorInterface<mixed, mixed> */
final readonly class ImageWriteProcessor implements ProcessorInterface {
    public function __construct(
        private ProcessorInterface $decorated,
        private EntityManagerInterface $em,
        private ImageLifecycleLock $lock,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed {
        return $this->em->wrapInTransaction(function () use ($data, $operation, $uriVariables, $context): mixed {
            $this->lock->acquire();

            return $this->decorated->process($data, $operation, $uriVariables, $context);
        });
    }
}
