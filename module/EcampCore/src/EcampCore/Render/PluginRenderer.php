<?php

namespace EcampCore\Render;

use Zend\View\Model\ViewModel;

use EcampCore\DI\DependencyLocator;
use EcampCore\Entity\Medium;
use EcampCore\Entity\PluginInstance;
use EcampCore\Repository\Provider\MediumRepositoryProvider;

class PluginRenderer
    extends DependencyLocator
    implements MediumRepositoryProvider
{

    /**
     * @param  PluginInstance            $pluginInstance
     * @param  Medium                    $medium
     * @return \Zend\View\Model\ViewModel
     */
    public function render(PluginInstance $pluginInstance, Medium $medium = null)
    {
        $medium = $medium ?: $this->ecampCore_MediumRepo()->getDefualtMedium();
        $view = $pluginInstance->getStrategyInstance()->render($medium);

        if ($view instanceof ViewModel) {
            return $view;
        } else {
            return new ViewModel($view);
        }
    }
}
