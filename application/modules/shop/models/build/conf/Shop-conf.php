<?php
// This file generated by Propel 1.5.3 convert-conf target
// from XML runtime conf file /home/firstrow/public_html/imagecms/application/modules/shop/models/runtime-conf.xml
$conf = array (
  'datasources' => 
  array (
    'Shop' => 
    array (
      'adapter' => 'mysql',
      'connection' => 
      array (
        'dsn' => 'mysql:host=localhost;dbname=imagecms',
        'user' => 'root',
        'password' => 'mysqlpass',
        'settings' => 
        array (
          'charset' => 
          array (
            'value' => 'utf8',
          ),
        ),
      ),
    ),
    'default' => 'Shop',
  ),
  'log' => 
  array (
    'type' => 'file',
    'name' => './propel.log',
    'ident' => 'propel',
    'level' => '7',
    'conf' => '',
  ),
  'generator_version' => '1.5.3',
);
$conf['classmap'] = include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classmap-Shop-conf.php');
return $conf;