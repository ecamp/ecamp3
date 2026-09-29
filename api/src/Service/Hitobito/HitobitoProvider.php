<?php

declare(strict_types=1);

namespace App\Service\Hitobito;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

enum HitobitoProvider: string {
    case PBSMIDATA = 'pbsmidata';
    case CEVIDB = 'cevidb';
    case JUBLADB = 'jubladb';

    public function isSupported(): bool {
        return match ($this) {
            self::PBSMIDATA => true,
            self::CEVIDB, self::JUBLADB => false,
        };
    }

    /**
     * Parses a given string to a HitobitoProvider, throwing if no matching provider can be found.
     */
    public static function parse(string $value): HitobitoProvider {
        $enum = HitobitoProvider::tryFrom($value);
        if (null === $enum || !$enum->isSupported()) {
            throw new NotFoundHttpException("Invalid Hitobito provider \"{$value}\"");
        }

        return $enum;
    }
}
