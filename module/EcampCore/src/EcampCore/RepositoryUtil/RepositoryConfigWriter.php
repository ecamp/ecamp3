<?php

namespace EcampCore\RepositoryUtil;

use Zend\Config\Config;

class RepositoryConfigWriter
    extends WriterBase
{

    public function writeRepositoryConfigs()
    {
        $config = new Config($this->serviceLocator->get('config'));

        foreach ($config->ecamp->modules as $module) {
            $this->writeRepositoryConfig(
                $module->repos->module_namespace,
                $module->repos->config_file,
                $module->repos->traits_namespace
            );
        }

    }

    private function writeRepositoryConfig(
        $moduleNamespace,
        $repositoryConfigFile,
        $traitNamespace
    ){
        $repositoryFactories = array();
        $repositoryAliases = array();

        $repositoryies = $this->getRepositories($moduleNamespace);

        foreach ($repositoryies as $repository) {

            $repositoryFactories[] = str_replace(
                array(
                    '/*ENTITY-CLASS*/',
                    '/*REPOSITORY-ALIAS*/'
                ),
                array(
                    $repository,
                    $this->getRepositoryAlias($repository)
                ),
                file_get_contents(__DIR__ . '/tpl/service.config.repos.factory.tpl')
            );

            $repositoryCases[] = str_replace(
                array(
                    '/*TRAIT-CLASS*/',
                    '/*SETTER-METHOD*/',
                    '/*REPOSITORY-ALIAS*/'
                ),
                array(
                    $this->getRepositoryTrait($traitNamespace, $repository),
                    $this->getSetterMethod($repository),
                    $this->getRepositoryAlias($repository)
                ),
                file_get_contents(__DIR__ . '/tpl/service.config.repos.initializer.tpl')
            );
        }

        $src = str_replace(
            array("/*REPOSITORY-FACTORY*/", "/*REPOSITORY-CASE*/"),
            array(implode(PHP_EOL, $repositoryFactories), implode(PHP_EOL, $repositoryCases)),
            file_get_contents(__DIR__ . '/tpl/service.config.repos.tpl'));

        file_put_contents($repositoryConfigFile, $src);

        return $src;
    }

}
