<?php

return array(


    'pdf' => array(
        'enabled' => true,
        // 'binary' => base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64'),
        'binary' => base_path('vendor/h4cc/wkhtmltopdf/bin/wkhtmltopdf.exe'),
        'timeout' => false,
        'options' => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary' => '/usr/local/bin/wkhtmltoimage',
        'timeout' => false,
        'options' => array(),
    ),


);
