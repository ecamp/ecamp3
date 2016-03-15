<?php

return array(
    'wkhtmltopdf' => array(
        'config' => array(
            // 'binary' => __VENDOR__ . '/profburial/wkhtmltopdf-binaries-osx/bin/wkhtmltopdf-amd64-osx',
            'tmpDir' => __DATA__ . '/tmp',
            'encoding' => 'UTF-8',
            'page-size' => 'A2',

            'disable-smart-shrinking',
            'enable-internal-links',

            'no-outline',
            'margin-top'    => 40,
            'margin-bottom' => 30,
            'margin-left'   => 0,
            'margin-right'  => 0,
        )
    )
);
