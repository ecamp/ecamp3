<?php

namespace EcampApi\Resource\User;

use EcampCore\Entity\Image;
use EcampLib\Resource\BaseResourceListener;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\PhpEnvironment\Request;

class ImageResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\UserRepository
     */
    protected function getUserRepository()
    {
        return $this->getService('EcampCore\Repository\User');
    }

    /**
     * @return \EcampCore\Service\ImageService
     */
    protected function getImageService()
    {
        return $this->getService('EcampCore\Service\Image');
    }

    /**
     * @param  ResourceEvent          $e
     * @return \EcampCore\Entity\User
     */
    protected function getUser(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $user = $this->getUserRepository()->find($id);

        if (!$user) {
            throw new DomainException('User not found', 404);
        }

        return $user;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('update', array($this, 'onUpdate'));
        $this->listeners[] = $events->attach('delete', array($this, 'onDelete'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $user = $this->getUser($e);
        $image = $user->getImage();

        if ($image != null) {
            if ($e->getQueryParam('show') !== null) {

                header('Content-Transfer-Encoding: binary');
                header('Content-Type: ' . $image->getMime());
                header('Content-Length: ' . $image->getSize());

                die($image->getData());
            } else {
                return new UserImageResource($user, $image);
            }
        }

        return null;
    }

    public function onUpdate(ResourceEvent $e)
    {
        $user = $this->getUser($e);
        $image = $user->getImage();

        if (!$image) {
            $image = $this->getImageService()->Create($user);
        }

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getService('Request');
        $data = $request->getContent();

        $this->getImageService()->Save($image, $data);

        return new UserImageResource($user, $image);
    }

    public function onDelete(ResourceEvent $e)
    {
        $user = $this->getUser($e);
        $user->delImage();

        return true;
    }
}
