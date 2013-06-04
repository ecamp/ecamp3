<?php

namespace EcampStoryboard;

use Zend\View\Model\ViewModel;

use EcampCore\Entity\Medium;
use EcampCore\Plugin\AbstractStrategy;

class Strategy extends AbstractStrategy
{

    private $sectionRepo;

    /**
     * @return Doctrine\ORM\EntityRepository
     */
    protected function getStoryboardRepo()
    {
        if ($this->sectionRepo == null) {
            $this->sectionRepo = $this->getServiceLocator()->get('ecampstoryboard.repo.section');
        }

        return $this->sectionRepo;
    }

    /**
     * @see EcampCore\Plugin.AbstractStrategy::render()
     */
    public function render(Medium $medium)
    {
        $sections =
            $this->getStoryboardRepo()->findBy(array(
                'pluginInstance' => $this->getPluginInstance()
            ));

        $view = new ViewModel();
        $view->setVariable('sections', $sections);
        $view->setTemplate($this->getTemplate($medium));

        return $view;
    }

    private function getTemplate(Medium $medium)
    {
        return 'ecamp-storyboard/' . $medium->getName();
    }

    public function renderBackend(){}
    public function renderFrontend(){}
}
