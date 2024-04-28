<?php

declare(strict_types=1);

namespace App\Tests\HttpCache\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class BaseEntity {
    /**
     * @var null|int The id
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    public function getId() {
        return $this->id;
    }

    public function setId($id): void {
        $this->id = $id;
    }
}
