<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * view_period_material_items
 * ManyToMany between Period and MaterialItem (based on a database view).
 */
#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'view_period_material_items')]
class PeriodMaterialItem {
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32, nullable: false)]
    public string $id;

    #[ORM\ManyToOne(targetEntity: Period::class, inversedBy: 'periodMaterialItems')]
    public Period $period;

    #[ORM\ManyToOne(targetEntity: MaterialItem::class, inversedBy: 'periodMaterialItems')]
    public MaterialItem $materialItem;
}
