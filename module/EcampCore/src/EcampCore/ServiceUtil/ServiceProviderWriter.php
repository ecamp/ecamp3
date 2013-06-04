<?php

namespace EcampCore\ServiceUtil;

use Zend\Code\Reflection\FileReflection;

use Zend\Config\Config;

use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceProviderWriter
{

    private $tmpl =
"<?php

namespace <<SERVICE-NAMESPACE>>;

/**
 * @method <<SERVICE-CLASS>> <<PROVIDER-METHOD>>()
 */
interface <<INTERFACE-NAME>>{}";

    /**
     * @var Zend\ServiceManager\ServiceLocatorInterface
     */
    private $serviceLocator;

    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function writeServiceProviderInterfaces()
    {
        $config = new Config($this->serviceLocator->get('config'));

        foreach ($config->ecamp->modules as $module) {
            $this->writeModuleServiceProviderInterfaces(
                    $module->services->services_path,
                    $module->services->config_file
            );
        }
    }

    private function writeModuleServiceProviderInterfaces($servicePath, $configFile)
    {
        foreach (scandir($servicePath) as $k => $v) {
            if (is_file($servicePath . $v) && $v != 'ServiceBase.php') {

                ob_start();
                require_once $servicePath . $v;
                ob_end_clean();

                $file = new FileReflection($servicePath . $v);
                $classes = $file->getClasses();

                if (count($classes)) {
                    $class = $classes[0];

                    $serviceClass = $class->getName();
                    $internalServiceAlias = substr(str_replace('\service\\', '.internal.service.', strtolower($serviceClass)), 0, -7);
                    $serviceAlias = substr(str_replace('\service\\', '.service.', strtolower($serviceClass)), 0, -7);
                    $serviceMethod = lcfirst(str_replace('\Service\\', '_', $serviceClass));

                    $serviceName = $this->getClassName($serviceClass);
                    $providerName = $serviceName . 'Provider';
                    $providerFile = $servicePath . 'Provider/' . $providerName . '.php';
                    $providerNamespace = $class->getNamespaceName() . '\Provider';

                    $providerMethod = str_replace('\Service\\', '_', $serviceClass);
                    $providerMethod = lcfirst($providerMethod);

                    $this->writeServiceProviderInterface(
                        $providerFile,
                        $providerName,
                        $providerNamespace,
                        $serviceClass,
                        $providerMethod
                    );
                }
            }
        }
    }

    private function writeServiceProviderInterface(
        $providerFile,
        $providerName,
        $repoNamespace,
        $repoClass,
        $providerMethod
    ){
        $src = str_replace(
            array("<<SERVICE-NAMESPACE>>", "<<SERVICE-CLASS>>", "<<PROVIDER-METHOD>>", "<<INTERFACE-NAME>>"),
            array($repoNamespace, $repoClass, $providerMethod, $providerName),
            $this->tmpl
        );

        file_put_contents($providerFile, $src);
    }

    private function getClassName($fqcn)
    {
        if (strstr($fqcn, '\\')) {
            return substr($fqcn, strrpos($fqcn, '\\') + 1);
        } else {
            return $fqn;
        }
    }

}
