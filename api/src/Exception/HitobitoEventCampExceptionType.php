<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * @see HitobitoEventCampException
 */
enum HitobitoEventCampExceptionType: string {
    // Camp exists, user does not have access
    case CAMP_FORBIDDEN = 'hitobito-camp-forbidden';
    // Camp does not exist, Hitobito event exists, user does not have access to the Hitobito event
    case EVENT_FORBIDDEN = 'hitobito-event-forbidden';
    // Camp does not exist, Hitobito event exists
    case CAMP_NOT_FOUND = 'hitobito-camp-not-found';
    // Camp does not exist, Hitobito event does not exist
    case EVENT_NOT_FOUND = 'hitobito-event-not-found';

    public function getType(): string {
        return '/errors/'.$this->value;
    }

    public function getTitle(): string {
        return match ($this) {
            self::CAMP_FORBIDDEN, self::EVENT_FORBIDDEN => 'Forbidden',
            self::CAMP_NOT_FOUND, self::EVENT_NOT_FOUND => 'Not Found',
        };
    }

    public function getStatus(): int {
        return match ($this) {
            self::CAMP_FORBIDDEN, self::EVENT_FORBIDDEN => Response::HTTP_FORBIDDEN,
            self::CAMP_NOT_FOUND, self::EVENT_NOT_FOUND => Response::HTTP_NOT_FOUND,
        };
    }

    public function getDefaultDetail(): string {
        return match ($this) {
            self::CAMP_FORBIDDEN => "A camp exists for this event, but you don't have access to it. Please contact your camp administrator.",
            self::EVENT_FORBIDDEN => "A Hitobito event was found, but you don't have permission to import it.",
            self::CAMP_NOT_FOUND => 'No camp has been created for this event yet.',
            self::EVENT_NOT_FOUND => 'No Hitobito event or camp could be found for this ID.',
        };
    }
}
