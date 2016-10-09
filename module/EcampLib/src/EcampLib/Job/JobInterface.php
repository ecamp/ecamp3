<?php

namespace EcampLib\Job;

use Zend\Mvc\ApplicationInterface;

/**
 * Interface JobInterface
 * @package EcampLib\JobEngine
 */
interface JobInterface extends \Serializable
{
    public function id($id = null);

    public function execute(ApplicationInterface $app);
}
