<?php

namespace EcampCore\Controller;

use EcampLib\Controller\AbstractBaseController;
use EcampCore\Entity\Image;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Http\Header\IfNoneMatch;

class AvatarController
    extends AbstractBaseController
{

    /**
     * @return \EcampCore\Service\AvatarService
     */
    private function getAvatarService()
    {
        return $this->serviceLocator->get('EcampCore\Service\Avatar');
    }

    public function userAction()
    {
        $user = $this->getRouteUser();
        $image = $this->getAvatarService()->GetUserAvatar($user);

        return $this->sendImage($image);
    }

    public function groupAction()
    {
        $group = $this->getRouteGroup();
        $image = $this->getAvatarService()->GetGroupAvatar($group);

        return $this->sendImage($image);
    }

    private function sendImage(Image $image)
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        if ($response instanceof Response && $request instanceof Request) {

            $expireTime = 86400;
            $etag = md5($image->getData());
            $modified = $image->getUpdatedAt();

            $response->getHeaders()
                ->addHeaderLine('Content-Type', $image->getMime())
                ->addHeaderLine('Cache-Control', 'max-age=' . $expireTime)
                ->addHeaderLine('Expires', gmdate('D, d M Y H:i:s', time() + $expireTime) . ' GMT')
                ->addHeaderLine('Last-Modified', gmdate('D, d M Y H:i:s', $modified->getTimestamp()) . ' GMT')
                ->addHeaderLine('ETag', $etag)
            ;

            $pageIsUnchanged =
                $request->getHeader('If-Modified-Since') &&
                strtotime($request->getHeader('If-Modified-Since')) > $modified;

            $etagsMatch =
                $request->getHeader('If-None-Match') instanceof IfNoneMatch &&
                $request->getHeader('If-None-Match')->getFieldValue() == $etag;

            if ($pageIsUnchanged || $etagsMatch) {
                // Send StatusCode 304 Not Modified
                $response->setStatusCode(304);
                $response->getHeaders()->addHeaderLine('Connection', 'close');
                $response->setContent("");
            } else {
                $response->setStatusCode(200);

                $response->setContent($image->getData());
                $response->getHeaders()->addHeaderLine('Content-Length', $image->getSize());
            }
        }

        return $response;
    }

}
