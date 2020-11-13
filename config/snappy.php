<?php

return array(


    'pdf' => array(
        'enabled' => true,
        'binary' => '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe"', // /usr/local/bin/wkhtmltopdf
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary' => '"C:\Program Files\wkhtmltopdf\bin\wkhtmltoimage.exe"', // /usr/local/bin/wkhtmltoimage
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),


);
