<?php

namespace EcampCore\Job;

use EcampLib\Job\AbstractBootstrappedJobBase;

class BasePdfPrinter extends AbstractBootstrappedJobBase
{
    public function __construct($url = null)
    {
        parent::__construct();

        $this->url = $url;
        $this->options = array();
        $this->timeout = false;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }
    }

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function perform()
    {
        $wkHtmlToPdf = new \EcampLib\Pdf\WkHtmlToPdf();
        $wkHtmlToPdf->setOptions($this->options);
        $wkHtmlToPdf->setTimeout($this->timeout);

        $wkHtmlToPdf->generate($this->url, __DATA__ . "/printer/" . $this->getToken() . ".pdf");
    }

}
