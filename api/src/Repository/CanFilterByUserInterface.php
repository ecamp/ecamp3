<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

interface CanFilterByUserInterface {
    public function filterByUser(QueryBuilder $queryBuilder, User $user);
}
