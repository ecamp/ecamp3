<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Util\IdGenerator;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\MappedSuperclass]
#[ORM\Index(columns: ['createTime'])]
#[ORM\Index(columns: ['updateTime'])]
abstract class BaseEntity {
    /**
     * An internal, unique, randomly generated identifier of this entity.
     */
    #[ApiProperty(writable: false, example: '1a2b3c4d')]
    #[Groups(['read'])]
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 16, nullable: false)]
    protected string $id;

    /**
     * @Gedmo\Timestampable(on="create")
     */
    #[ApiProperty(writable: false)]
    #[ORM\Column(type: 'datetime')]
    protected DateTime $createTime;

    /**
     * @Gedmo\Timestampable(on="update")
     */
    #[ApiProperty(writable: false)]
    #[ORM\Column(type: 'datetime')]
    protected DateTime $updateTime;

    public function __construct() {
        $this->id = IdGenerator::generateRandomHexString(12);
    }

    public function getId(): string {
        return $this->id;
    }
}
