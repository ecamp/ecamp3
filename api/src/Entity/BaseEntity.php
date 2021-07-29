<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Util\IdGenerator;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\MappedSuperclass
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"createTime"}),
 *     @ORM\Index(columns={"updateTime"})
 * })
 */
abstract class BaseEntity {
    /**
     * An internal, unique, randomly generated identifier of this entity.
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=16, nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=IdGenerator::class)
     */
    #[ApiProperty(writable: false, example: '1a2b3c4d')]
    #[Groups(['read'])]
    protected string $id;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    #[ApiProperty(writable: false)]
    protected DateTime $createTime;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    #[ApiProperty(writable: false)]
    protected DateTime $updateTime;

    public function getId(): string {
        return $this->id;
    }
}
