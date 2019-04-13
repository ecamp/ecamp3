<?php
namespace eCampApi\V1\Rpc\Index;

class IndexControllerFactory
{
    public function __invoke($controllers)
    {
        return new IndexController();
    }
}
