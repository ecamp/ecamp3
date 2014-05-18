<?php

namespace EcampLib\Log;

use Traversable;
use Zend\Log\Exception\InvalidArgumentException;
use Zend\Stdlib\ArrayUtils;

class Logger extends \Zend\Log\Logger
{
    private $user;

    private $url;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = (string) $user;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = (string) $url;
    }

    public function log($priority, $message, $extra = array(), $stacktrace = null)
    {
        if (!is_array($extra) && !$extra instanceof Traversable) {
            throw new InvalidArgumentException(
                '$extra must be an array or implement Traversable'
            );
        } elseif ($extra instanceof Traversable) {
            $extra = ArrayUtils::iteratorToArray($extra);
        }

        $extra['_user_'] = $this->getUser();
        $extra['_url_'] = $this->getUrl();

//        if ($priority <= self::ERR) {
//            $extra['_stacktrace_'] = $stacktrace ?: print_r(debug_backtrace(), true);
//        }

        parent::log($priority, $message, $extra);
    }

}
