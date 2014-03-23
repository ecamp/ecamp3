<?php
/**
 * Dummy action for a resque job that needs Application dependencies
 *
 */
namespace EcampCore\Job;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;
use Zend\Math\Rand;

class JobController extends AbstractActionController
{

    public function dummyAction(){
        $request = $this->getRequest();

        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('You can only use this action from a console!');
        }

        // Get parameters
        $parameter   = $request->getParam('parameter');

        return "Dummy action says: $parameter \n";
    }
}