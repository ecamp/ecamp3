<?php

namespace App\Metadata\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;

class OperationHelper {
    public static function findOneByType(ResourceMetadataCollection $collection, string $operationClassName): ?Operation {
        $it = $collection->getIterator();

        while ($it->valid()) {
            /** @var ApiResource $metadata */
            $metadata = $it->current();

            foreach ($metadata->getOperations() ?? [] as $operation) {
                if ($operation instanceof $operationClassName) {
                    return $operation;
                }
            }

            foreach ($metadata->getGraphQlOperations() ?? [] as $operation) {
                if ($operation instanceof $operationClassName) {
                    return $operation;
                }
            }

            $it->next();
        }

        return null;
    }
}
