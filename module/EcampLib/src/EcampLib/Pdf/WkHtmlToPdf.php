<?php

namespace EcampLib\Pdf;

use Knp\Snappy\Pdf;

class WkHtmlToPdf extends Pdf
{
    public function __construct()
    {
        $os = strtolower(PHP_OS);
        $binPath = null;

        // Mac OS X
        if ($os == 'darwin') {
            $binPath = __BASE__ . '/vendor/messagedigital/wkhtmltopdf-osx/bin/wkhtmltopdf-osx';

        // Linux / Unix
        } elseif ($os == 'unix' || $os == 'linux') {
            $binPath = (strstr(php_uname('m'), '64') > 0)
                ? __BASE__ . '/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64'
                : __BASE__ . '/vendor/h4cc/wkhtmltopdf-i386/bin/wkhtmltopdf-i386';

        // Windows
        } elseif (substr($os, 0, 3) == 'win') {
            $binPath = null;

        }

        parent::__construct($binPath);
    }
}
