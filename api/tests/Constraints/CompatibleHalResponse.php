<?php

namespace App\Tests\Constraints;

use PHPUnit\Framework\Constraint\Constraint;

class CompatibleHalResponse extends Constraint {
    public function __construct(private readonly array $halResponse) {
    }

    public static function isHalCompatibleWith(array $halResponse): CompatibleHalResponse {
        return new CompatibleHalResponse($halResponse);
    }

    public function toString(): string {
        return 'is hal compatible with '.$this->exporter()->export($this->halResponse);
    }

    protected function matches($other): bool {
        if (!is_array($other)) {
            return false;
        }
        $halResponseKeys = array_keys($this->halResponse);
        sort($halResponseKeys);
        $halResponseKeysWithoutEmbedded = array_diff($halResponseKeys, ['_embedded']);

        $otherKeys = array_keys($other);
        sort($otherKeys);
        $otherKeysWithoutEmbedded = array_diff($otherKeys, ['_embedded']);

        if (join($halResponseKeysWithoutEmbedded) !== join($otherKeysWithoutEmbedded)) {
            return false;
        }

        $halResponseLinksKeys = array_keys($this->halResponse['_links'] ?? []);
        $halResponseEmbeddedKeys = array_keys($this->halResponse['_embedded'] ?? []);
        $halResponseRelationKeys = array_unique(array_merge($halResponseLinksKeys, $halResponseEmbeddedKeys));
        sort($halResponseRelationKeys);

        $otherLinksKeys = array_keys($other['_links'] ?? []);
        $otherEmbeddedKeys = array_keys($other['_embedded'] ?? []);
        $otherRelationKeys = array_unique(array_merge($otherLinksKeys, $otherEmbeddedKeys));
        sort($otherRelationKeys);

        if (join($halResponseRelationKeys) !== join($otherRelationKeys)) {
            return false;
        }

        foreach ($halResponseRelationKeys as $key) {
            if (isset($this->halResponse['_embedded'][$key], $other['_embedded'][$key])) {
                $compatibleHalResponse = new CompatibleHalResponse($this->halResponse['_embedded'][$key]);
                if (!$compatibleHalResponse->matches($other['_embedded'][$key])) {
                    return false;
                }
            }
        }

        return true;
    }
}
