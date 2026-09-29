<?php

declare(strict_types=1);

namespace App\Exception;

use ApiPlatform\Metadata\Exception\ProblemExceptionInterface;

/**
 * Contains exceptions related to the HitobitoEventCamp (deep-link) logic.
 */
class HitobitoEventCampException extends \RuntimeException implements ProblemExceptionInterface {
    public function __construct(private readonly HitobitoEventCampExceptionType $exceptionType) {
        parent::__construct($exceptionType->getDefaultDetail());
    }

    public function getType(): string {
        return $this->exceptionType->getType();
    }

    public function getTitle(): ?string {
        return $this->exceptionType->getTitle();
    }

    public function getStatus(): ?int {
        return $this->exceptionType->getStatus();
    }

    public function getDetail(): ?string {
        return $this->getMessage();
    }

    public function getInstance(): ?string {
        return null;
    }
}
