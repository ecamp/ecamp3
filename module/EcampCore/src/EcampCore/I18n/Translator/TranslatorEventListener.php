<?php

namespace EcampCore\I18n\Translator;

use BsbDoctrineTranslationLoader\Entity\Locale;
use BsbDoctrineTranslationLoader\Entity\Message;
use Doctrine\ORM\EntityManager;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\I18n\Translator\Translator;

class TranslatorEventListener extends AbstractListenerAggregate
{
    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(Translator::EVENT_NO_MESSAGES_LOADED, array($this, 'noMessagesLoaded'));
        $this->listeners[] = $events->attach(Translator::EVENT_MISSING_TRANSLATION, array($this, 'missingTranslation'));
    }

    public function noMessagesLoaded(Event $e)
    {
        // var_dump($e);
    }

    public function missingTranslation(Event $e)
    {
        // var_dump($e);

        $localeRepo = $this->em->getRepository('BsbDoctrineTranslationLoader\Entity\Locale');
        $messageRepo = $this->em->getRepository('BsbDoctrineTranslationLoader\Entity\Message');

        $textDomain = $e->getParam('text_domain');
        $textMessage = $e->getParam('message');

        /** @var Locale $locale */
        $locale = $e->getParam('locale');
        $locale = $localeRepo->findOneBy(array(
            'locale' => $locale
        ));

        if ($locale) {
            $message = $messageRepo->findOneBy(array(
                'locale' => $locale,
                'domain' => $textDomain,
                'message' => $textMessage
            ));

            if ($message == null) {
                $message = new Message();
                $message->setLocale($locale);
                $message->setDomain($textDomain);
                $message->setMessage($textMessage);

                $this->em->persist($message);
            }
        }

        // return new TranslationMissing($textMessage);
    }
}

/*
class TranslationMissing
{
    private $textMessage;

    public function __construct($textMessage)
    {
        $this->textMessage = $textMessage;
    }

    public function __toString()
    {
        return $this->textMessage . ' [Translation missing]';
    }

    public function __get($key)
    {
        return $this->textMessage . ' [Plural Translation missing]';
    }
}
*/
