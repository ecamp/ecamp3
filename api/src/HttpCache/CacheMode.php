<?php

declare(strict_types=1);

namespace App\HttpCache;

enum CacheMode: string {
    /** Cached separately for every login. */
    case User = 'user';

    /** Cached once for all users who may read the security delegate of the operation. */
    case Shared = 'shared';

    /** Cached once for everyone, no access check. */
    case Public = 'public';
}
