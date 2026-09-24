<?php

declare(strict_types=1);

namespace App\Exception;

use ApiPlatform\Metadata\Exception\ProblemExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Thrown when the Hitobito access token cookie for a provider is missing, or the token it
 * contains cannot be decrypted/verified for the current user.
 */
class HitobitoAccessTokenInvalidException extends \RuntimeException implements ProblemExceptionInterface {
    public function getType(): string {
        return '/errors/hitobito-access-token-invalid';
    }

    public function getTitle(): ?string {
        return 'Hitobito access token is invalid';
    }

    public function getStatus(): ?int {
        return Response::HTTP_FORBIDDEN;
    }

    public function getDetail(): ?string {
        return '' !== $this->getMessage() ? $this->getMessage() : null;
    }

    public function getInstance(): ?string {
        return null;
    }
}
