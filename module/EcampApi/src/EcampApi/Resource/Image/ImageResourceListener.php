<?php

namespace EcampApi\Resource\Image;

use EcampCore\Resource\BaseResourceListener;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class ImageResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\ImageRepository
     */
    protected function getImageRepository()
    {
        return $this->getService('EcampCore\Repository\Image');
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        //$this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');

        /** @var \EcampCore\Entity\Image $image */
        $image = $this->getImageRepository()->find($id);

        if (!$image) {
            throw new DomainException('Image not found', 404);
        }

        return new ImageResource($image);
    }

}
