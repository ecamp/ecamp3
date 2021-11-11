<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Util\IdGenerator;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

#[MappedSuperclass]
#[Index(columns: ['createTime'])]
#[Index(columns: ['updateTime'])]
abstract class BaseEntity {
    /**
     * An internal, unique, randomly generated identifier of this entity.
     */
    #[ApiProperty(writable: false, example: '1a2b3c4d')]
    #[Groups(['read'])]
    #[Id]
    #[Column(type: 'string', length: 16, nullable: false)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: IdGenerator::class)]
    protected string $id;
    /**
     * @Gedmo\Timestampable(on="create")
     */
    #[ApiProperty(writable: false)]
    #[Column(type: 'datetime')]
    protected DateTime $createTime;
    /**
     * @Gedmo\Timestampable(on="update")
     */
    #[ApiProperty(writable: false)]
    #[Column(type: 'datetime')]
    protected DateTime $updateTime;

    public function getId(): string {
        return $this->id;
    }
}
