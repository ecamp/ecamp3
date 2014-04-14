<?php

namespace EcampCore\Job;

abstract class BaseJob
{
    public function perform()
    {
        $this->{array_shift($this->args)}();
    }

}
