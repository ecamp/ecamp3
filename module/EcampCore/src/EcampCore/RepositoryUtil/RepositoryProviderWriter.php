<?php

namespace EcampCore\RepositoryUtil;

use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class RepositoryProviderWriter
{

    private $tmpl =
"<?php

namespace <<REPO-NAMESPACE>>;

/**
 * @method <<REPO-CLASS>> <<PROVIDER-METHOD>>()
 */
interface <<INTERFACE-NAME>>{}";

    /**
     * @var Zend\ServiceManager\ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(
            ServiceLocatorInterface $serviceLocator,
            EntityManager $em
    ){
        $this->serviceLocator = $serviceLocator;
        $this->em = $em;
    }

    public function writeRepositoryProviderInterfaces()
    {
        $classMetadataList = $this->em->getMetadataFactory()->getAllMetadata();
        foreach ($classMetadataList as $classMetadata) {
            if (! $classMetadata->isMappedSuperclass) {

                $repoDirectory = dirname(dirname($classMetadata->reflClass->getFileName())) . '/Repository';
                $repoNamespace = str_replace('Entity', 'Repository\Provider', $classMetadata->reflClass->getNamespaceName());

                $repoClass = $classMetadata->customRepositoryClassName ?: 'Doctrine\ORM\EntityRepository';

                if ($classMetadata->customRepositoryClassName) {
                    $repositoryName = $this->getClassName($classMetadata->customRepositoryClassName);
                } else {
                    $repositoryName = $this->getClassName($classMetadata->name) . 'Repository';
                }
                $providerName = $repositoryName . 'Provider';
                $providerFile = $repoDirectory . '/Provider/' . $providerName . '.php';

                $providerMethod = str_replace('\Entity\\', '_', $classMetadata->name);
                $providerMethod = lcfirst($providerMethod) . "Repo";

                $this->writeRepositoryProviderInterface(
                    $providerFile,
                    $providerName,
                    $repoNamespace,
                    $repoClass,
                    $providerMethod
                );
            }
        }
    }

    private function writeRepositoryProviderInterface(
        $providerFile,
        $providerName,
        $repoNamespace,
        $repoClass,
        $providerMethod
    ){
        $src = str_replace(
            array("<<REPO-NAMESPACE>>", "<<REPO-CLASS>>", "<<PROVIDER-METHOD>>", "<<INTERFACE-NAME>>"),
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
