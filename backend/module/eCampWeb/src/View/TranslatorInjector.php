<?php

namespace eCamp\Web\View;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class TranslatorInjector extends AbstractListenerAggregate {
    public const SESSION_NAMESPACE = self::class;


    /** @var TranslatorInterface */
    private $translator;

    /** @var Container */
    private $sessionContainer;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }


    public function attach(EventManagerInterface $events, $priority = 1) {
        $this->listeners = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], -1000);
    }

    public function onDispatch(MvcEvent $e) {
        /** @var Request $req */
        $req = $e->getRequest();
        $locale = $req->getQuery('locale');
        if ($locale != null) {
            $this->setLocale($locale);
        }

        /** @var Translator $translator */
        $translator = $this->translator;
        $translator->setFallbackLocale('en');
        $translator->setLocale($this->getLocale());

        $res = $e->getResult();
        if ($res instanceof ViewModel) {
            $res->setVariable('translator', $this->translator);
        }
    }


    public function getLocale() {
        if ($this->sessionContainer == null) {
            $this->sessionContainer = new Container(self::SESSION_NAMESPACE);
        }
        return $this->sessionContainer->locale;
    }

    public function setLocale($locale) {
        if ($this->sessionContainer == null) {
            $this->sessionContainer = new Container(self::SESSION_NAMESPACE);
        }
        $this->sessionContainer->locale = $locale;
    }
}
