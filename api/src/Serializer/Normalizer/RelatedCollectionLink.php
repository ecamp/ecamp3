<?php

namespace App\Serializer\Normalizer;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RelatedCollectionLink {
    public function __construct(protected string $uriTemplate, protected array $params = []) {
    }

    public function getUriTemplate(): string {
        return $this->uriTemplate;
    }

    public function getParams(): array {
        return $this->params;
    }
}
