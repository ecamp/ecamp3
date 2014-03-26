<?php

namespace EcampMaterial\Service;

use EcampMaterial\Entity\MaterialItem;

use Doctrine\ORM\EntityRepository;

use EcampLib\Service\ServiceBase;
use EcampCore\Entity\EventPlugin;

use EcampMaterial\Repository\ItemRepository;

class ItemService
    extends ServiceBase
{

    private $itemRepository;

    public function __construct(
        EntityRepository $itemRepository
    ){
        $this->itemRepository = $itemRepository;
    }

    public function create(EventPlugin $eventPlugin, array $data)
    {
        $item = new MaterialItem($eventPlugin);
        $this->update($item, $data);
        $this->persist($item);

        return $item;
    }

    public function update(MaterialItem $item, array $data)
    {
        $item->setQuantity($data['quantity']);
        $item->setText($data['text']);
    }

    public function delete(MaterialItem $item)
    {
        $this->remove($item);
    }

}
