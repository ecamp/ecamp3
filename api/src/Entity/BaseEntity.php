<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Util\IdGenerator;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\MappedSuperclass
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"createTime"}),
 *     @ORM\Index(columns={"updateTime"})
 * })
 */
abstract class BaseEntity {
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    public DateTime $createTime;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    public DateTime $updateTime;
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=16, nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=IdGenerator::class)
     */
    #[ApiProperty(writable: false)]
    protected string $id;

    public function getId(): string {
        return $this->id;
    }

    public function getCreateTime(): DateTime {
        return $this->createTime;
    }

    public function setCreateTime(DateTime $createTime): BaseEntity {
        $this->createTime = $createTime;

        return $this;
    }

    public function getUpdateTime(): DateTime {
        return $this->updateTime;
    }

    public function setUpdateTime(DateTime $updateTime): BaseEntity {
        $this->updateTime = $updateTime;

        return $this;
    }
}
