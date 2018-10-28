<?php
/**
 * Created by PhpStorm.
 * User: A.Khudenko
 * Date: 19.10.2018
 * Time: 07:18
 */
use App\Entity\User;

// config/packages/security.php
    $container->loadFromExtension('security', array(
        'encoders'=>array(
            User::class => array(
                'algorithm' => 'bcrypt',
                'cost' => 12,
            ),
            'Symfony\Component\Security\Core\User\User' => 'plaintext',
        ),
        'providers' => array(
            'our_db_provider' => array(
                'entity' => array(
                    'class'    => User::class,
                    'property' => 'email',
                ),
            ),
        ),
    'firewalls' => array(
        'dev' => array(
            'pattern'   => '^/(_(profiler|wdt)|css|images|js)/',
            'security'  => false,
        ),
        'main' => array(
            'anonymous' => null,
            'form_login'=>array(
                'login_path'=>'login',
                'check_path'=>'login',
            ),
            'logout' => array('path' => 'security_logout', 'target' => '/blog')
        ),
    ),
    'access_control' => array(
        // потребовать ROLE_ADMIN для /admin*
        array('path' => '^/login', 'role' => 'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('path' => '^/register', 'role' => 'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('path' => '^/admin', 'role' => 'ROLE_ADMIN'),

    ),
));