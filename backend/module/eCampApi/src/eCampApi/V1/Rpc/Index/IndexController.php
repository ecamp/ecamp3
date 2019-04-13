<?php
namespace eCampApi\V1\Rpc\Index;

use eCamp\Core\Entity\User;
use Zend\Mvc\Controller\AbstractActionController;
use ZF\Hal\Entity;
use ZF\Hal\Link\Link;
use ZF\Hal\View\HalJsonModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $data = [];
        $data['title'] = 'eCamp V3 - API';

        /** @var User $user */
        $user = null;
        $userId = null; // $this->authenticationService->getIdentity();
        if ($userId != null) {
            $user = null; // $this->userService->fetch($userId);
        }
        if ($user != null) {
            $data['user'] = $user->getDisplayName();
        } else {
            $data['user'] = 'guest';
        }

        $data['login'] = Link::factory([
            'rel' => 'login',
            'route' => 'e-camp-api.rpc.login'
        ]);

//        $camps = new Link('camps');
//        $camps->setRoute('ecamp.api.camp');
//        $data['camps'] = $camps;

        $json = new HalJsonModel();
        $json->setPayload(new Entity($data));

        return $json;
    }
}
