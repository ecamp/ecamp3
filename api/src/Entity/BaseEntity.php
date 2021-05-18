<?php

namespace App\Entity;

use App\Util\IdGenerator;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Mtarld\SymbokBundle\Annotation\Getter;

/**
 * @ORM\MappedSuperclass
 * @ORM\Table(indexes={
 * @ORM\Index(columns={"create_time"}),
 * @ORM\Index(columns={"update_time"})
 * })
 */
abstract class BaseEntity {
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=16, nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=IdGenerator::class)
     * @Getter
     */
    protected string $id;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected DateTime $createTime;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected DateTime $updateTime;
}
