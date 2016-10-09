<?php

namespace EcampLib\Job;

use Zend\Mvc\ApplicationInterface;
use Zend\Mvc\Router\RouteStackInterface;
use Zend\View\View;

abstract class AbstractJobBase implements JobInterface
{
    private $id = null;
    private $storage = array();


    /** @var array */
    protected $config;

    /** @var RouteStackInterface */
    protected $router;

    /** @var View */
    protected $view;


    public function id($id = null)
    {
        if (isset($id)) {
            $this->id = $id;
        }
        return $this->id;
    }


    public function execute(ApplicationInterface $app)
    {
        $this->doInit($app);
        $this->doExecute($app);
    }

    public function doInit(ApplicationInterface $app)
    {
        $serviceManager = $app->getServiceManager();

        $this->config = $serviceManager->get('Config');
        $this->router = $serviceManager->get('HttpRouter');
        $this->view = $serviceManager->get('View');
    }

    abstract public function doExecute(ApplicationInterface $app);


    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->storage[$name]);
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function &get($name)
    {
        if ($this->has($name)) {
            return $this->storage[$name];
        }
        throw new \Exception('Unknown property: ' . $name);
    }

    /**
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function set($name, $value)
    {
        if (!$this->propertyValueIsValid($value)) {
            throw new \Exception("Only arrays und scalars (int, string, ...) are allowed.");
        }
        $this->storage[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function &__get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }


    /**
     * @return string
     */
    public function serialize()
    {
        $data = array(
            'storage' => $this->storage
        );
        return serialize($data);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->storage = $data['storage'];
    }


    /**
     * @param $value
     * @return bool
     */
    private function propertyValueIsValid($value)
    {
        if (is_object($value)) {
            return false;
        }
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                if (!$this->propertyValueIsValid($key)) {
                    return false;
                }
                if (!$this->propertyValueIsValid($item)) {
                    return false;
                }
            }
        }
        return true;
    }


    protected function urlFromRoute($name, $params = array(), $options = array())
    {
        $options['name'] = $name;
        return $this->router->assemble($params, $options);
    }
}
