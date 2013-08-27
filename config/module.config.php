<?php

namespace MyBackend;

return array(

    /**
     * MyBackend module
     */
    __NAMESPACE__ => array(
        'options' => array(
            'routes' => array(
                'frontend' => 'home',
                'backend' => 'admin',
                'login' => 'admin/login',
            ),
            'disable_frontend_login' => true,
        ),
        'view_params' => array(
            'cache_bust_index' => uniqid(),
            'title' => 'My Backend',
            'lang' => 'en',
        ),
    ),

    /**
     * ZfcRbac module
     */
    'zfcrbac' => array(
        'anonymousRole' => 'guest',
        'firewallController' => false,
        'firewallRoute' => true,
        'firewalls' => array(
            'ZfcRbac\Firewall\Route' => array(
                array('route' => 'admin/login', 'roles' => 'guest'),
                array('route' => 'admin/*', 'roles' => 'admin'),
            ),
        ),
        'providers' => array(
            'ZfcRbac\Provider\AdjacencyList\Role\DoctrineDbal' => array(
                'connection' => 'doctrine.connection.orm_default',
                'options' => array(
                    'table' => 'mbe_roles',
                    'id_column' => 'role_id',
                    'name_column' => 'name',
                    'join_column' => 'parent_role_id'
                )
            ),
            'ZfcRbac\Provider\Generic\Permission\DoctrineDbal' => array(
                'connection' => 'doctrine.connection.orm_default',
                'options' => array(
                    'permission_table' => 'mbe_permissions',
                    'role_table' => 'mbe_roles',
                    'role_join_table' => 'mbe_roles_permissions',
                    'permission_id_column' => 'permission_id',
                    'permission_join_column' => 'permission_id',
                    'role_id_column' => 'role_id',
                    'role_join_column' => 'role_id',
                    'permission_name_column' => 'name',
                    'role_name_column' => 'name'
                )
            )
        ),
    ),

    /**
     * ZfcUser module
     */
    'zfcuser' => array(
        'user_entity_class' => 'MyBackend\Entity\User',
        'enable_registration' => false,
        'enable_username' => true,
        'auth_identity_fields' => array( 'username', 'email' ),

        'enable_default_entities' => false /** ZfcUserDoctrineORM conf entry */
    ),

    /**
     * Doctrine module
     */
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' =>array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__
                )
            )
        )
    ),
    'data-fixture' => array(
        __NAMESPACE__ . '_fixtures' => __DIR__ . '/../resources/fixtures',
    ),

    /**
     * other zf2 configuration
     */

    'router' => array(
        'routes' => array(
            'admin' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/admin',
                    'defaults' => array(
                        'controller' => 'admin',
                        'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'login' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/login',
                            'defaults' => array(
                                'action' => 'login'
                            ),
                        ),
                    )
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'nav-backend' => __NAMESPACE__ . '\Service\BackendNavigationFactory',
        ),
        'aliases' => array(
            // this is needed by ZfcRbac
            'Zend\Authentication\AuthenticationService' => 'zfcuser_auth_service',

            // these are needed by ZfcUser
            'zfcuser_zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
            'zfcuser_doctrine_em' => 'Doctrine\ORM\EntityManager',
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'admin' => 'MyBackend\Controller\AdminController',
        ),
    ),

    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir'      => __DIR__ . '/../language',
                'pattern'       => '%s/'.__NAMESPACE__.'.php',
                'text_domain'   => __NAMESPACE__,
            ),
        ),
    ),
);
