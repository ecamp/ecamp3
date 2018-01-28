<?php

namespace eCamp\LibTest\PHPUnit;

use Doctrine\ORM\Tools\ToolsException;

abstract class AbstractHttpControllerTestCase extends AbstractDatabaseTestCase
{
    /**
     * @throws ToolsException
     */
    public function setUp() {
        $data = include __DIR__ . '/../../../../config/application.config.php';
        $this->setApplicationConfig($data);

        parent::setUp();
    }

}
