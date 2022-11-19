<?php

namespace App\Metadata\Resource;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;

class OperationHelper {
    public static function findOneByType(ResourceMetadataCollection $collection, string $className): ?Operation {
        $it = $collection->getIterator();
        $metadata = null;

        while ($it->valid()) {
            /** @var ApiResource $metadata */
            $metadata = $it->current();

            foreach ($metadata->getOperations() ?? [] as $operation) {
                if ($operation instanceof $className) {
                    return $operation;
                }
            }

            foreach ($metadata->getGraphQlOperations() ?? [] as $operation) {
                if ($operation instanceof $className) {
                    return $operation;
                }
            }

            $it->next();
        }

        return null;
    }
}
