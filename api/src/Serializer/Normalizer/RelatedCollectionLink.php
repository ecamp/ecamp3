<?php

namespace App\Serializer\Normalizer;

#[\Attribute(\Attribute::TARGET_METHOD)]
class RelatedCollectionLink {
    public function __construct(protected string $relatedEntity, protected array $params = []) {}

    public function getRelatedEntity(): string {
        return $this->relatedEntity;
    }

    public function getParams(): array {
        return $this->params;
    }
}
