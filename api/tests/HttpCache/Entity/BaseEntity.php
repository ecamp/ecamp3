<?php

declare(strict_types=1);

namespace App\Tests\HttpCache\Entity;

use App\Entity\HasId;
use Doctrine\ORM\Mapping as ORM;

abstract class BaseEntity implements HasId {
    /**
     * @var null|string The id
     */
    #[ORM\Column(type: 'string', nullable: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private string $id;

    public function getId(): string {
        return $this->id;
    }

    public function setId(string $id): void {
        $this->id = $id;
    }
}
