<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use App\Util\IdGenerator;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\MappedSuperclass]
#[ORM\Index(columns: ['createTime'])]
#[ORM\Index(columns: ['updateTime'])]
abstract class BaseEntity implements HasId {
    /**
     * An internal, unique, randomly generated identifier of this entity.
     */
    #[ApiProperty(writable: false, example: '1a2b3c4d')]
    #[Groups(['read'])]
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 16, nullable: false)]
    protected string $id;

    #[ApiProperty(writable: false)]
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    protected \DateTimeImmutable $createTime;

    #[ApiProperty(writable: false)]
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    protected \DateTimeImmutable $updateTime;

    public function __construct() {
        $this->id = IdGenerator::generateRandomHexString(12);
    }

    public function getId(): string {
        return $this->id;
    }
}
