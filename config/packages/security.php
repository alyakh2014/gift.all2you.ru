<?php
/**
 * Created by PhpStorm.
 * User: A.Khudenko
 * Date: 19.10.2018
 * Time: 07:18
 */

// config/packages/security.php
$container->loadFromExtension('security', array(
    'providers' => array(
        'in_memory' => array(
            'memory' => null,
        ),
    ),
    'firewalls' => array(
        'dev' => array(
            'pattern'   => '^/(_(profiler|wdt)|css|images|js)/',
            'security'  => false,
        ),
        'main' => array(
            'anonymous' => null,
            'http_basic'=>null
        ),
    ),
    'access_control' => array(
        // потребовать ROLE_ADMIN для /admin*
        array('path' => '^/admin', 'roles' => 'ROLE_ADMIN'),
    ),
));