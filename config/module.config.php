<?php

namespace MyBackend;

return [

    /**
     * MyBackend module
     */
    __NAMESPACE__ => [
        /**
         * these are the defaults provided by ModuleOptions
         */
//        'disable_frontend_login'    => true,
//        'backend_route'             => 'admin',
//        'frontend_route'            => 'home',
//        'backend_login_route'       => 'admin/login',
//        'post_logout_route'         => 'admin',
//        'template'                  => 'my-backend/layout/layout',
//        'cache_bust_index'          => uniqid(),
//        'title'                     => 'My Backend',
    ],

    'navigation' => [
        'backend' => [
        ],
    ],

    /**
     * ZfcRbac module
     */
    'zfc_rbac' => [
        'guards' => [
            'ZfcRbac\Guard\RouteGuard' => [
                'admin/login' => ['guest'],
                'admin*' => ['admin'],
            ],
        ],
        'role_provider' => [
            'ZfcRbac\Role\ObjectRepositoryRoleProvider' => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'class_name'     => 'MyBackend\Entity\Role',
                'role_name_property' => 'name',
            ],
        ],
    ],

    /**
     * ZfcUser module
     */
    'zfcuser' => [
        'user_entity_class' => 'MyBackend\Entity\User',
        'enable_registration' => false,
        'enable_username' => true,
        'auth_identity_fields' => [ 'username', 'email' ],

        'enable_default_entities' => false /** ZfcUserDoctrineORM conf entry */
    ],

    /**
     * Doctrine module
     */
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => [__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity']
            ],
            'orm_default' =>[
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__
                ]
            ]
        ]
    ],
    'data-fixture' => [
        __NAMESPACE__ . '_fixtures' => __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity/Fixture',
    ],

    /**
     * other zf2 configuration
     */

    'router' => [
        'routes' => [
            'admin' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/admin',
                    'defaults' => [
                        'controller' => 'admin',
                        'action' => 'index'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'login' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/login',
                            'defaults' => [
                                'action' => 'login'
                            ],
                        ],
                    ]
                ],
            ],
        ],
    ],

    'console' => [
        'router' => [
            'routes' => [
                'user-create-admin' => [
                    'options' => [
                        'route' => 'user create [--username=] [--email=] [--roles=]',
                        'defaults' => [
                            'controller' => __NAMESPACE__ . '\Controller\UserConsoleController',
                            'action' => 'create',
                        ],
                    ],
                ],
                'user-delete' => [
                    'options' => [
                        'route' => 'user delete [--username=] [--email=]',
                        'defaults' => [
                            'controller' => __NAMESPACE__ . '\Controller\UserConsoleController',
                            'action' => 'delete',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'service_manager' => [
        'invokables' => [
            'zfcuser_user_service'              => 'MyBackend\Service\UserService',
        ],
        'factories' => [
            'MyBackend\Options\ModuleOptions'           => 'MyBackend\Options\ModuleOptionsFactory',
            'zfcuser_user_mapper'                       => 'MyBackend\Mapper\UserMapperFactory',
            'MyBackend\Mapper\RoleMapper'               => 'MyBackend\Mapper\DoctrineRoleMapperFactory',
            'MyBackend\Navigation\BackendNavigation'    => 'MyBackend\Navigation\BackendNavigationFactory',
            'MyBackend\Navigation\BackendBreadcrumbs'   => 'MyBackend\Navigation\BackendBreadcrumbsFactory',
        ],
        'aliases' => [
            'MyBackend\Mapper\UserMapper'               => 'zfcuser_user_mapper',

            // this is needed by ZfcRbac
            'Zend\Authentication\AuthenticationService' => 'zfcuser_auth_service',
            // these are needed by ZfcUser
            'zfcuser_zend_db_adapter'                   => 'Zend\Db\Adapter\Adapter',
        ],
    ],

    'controllers' => [
        'invokables' => [
            'admin' => 'MyBackend\Controller\AdminController',
            'MyBackend\Controller\UserConsoleController' => 'MyBackend\Controller\UserConsoleController',
        ],
    ],

    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'phpArray',
                'base_dir'      => __DIR__ . '/../language',
                'pattern'       => '%s/'.__NAMESPACE__.'.php',
                'text_domain'   => __NAMESPACE__,
            ],
        ],
    ],
];
