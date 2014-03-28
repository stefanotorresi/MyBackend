<?php

namespace MyBackend;

use MyBackend\Entity\Fixture\RoleFixture;

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
//        'cache_bust_index'          => mt_rand(),
//        'title'                     => 'My Backend',
//        'load_default_user_mapping' => true,
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
                'admin/login' => [ RoleFixture::GUEST ],
                'admin*' => [ RoleFixture::ADMIN ],
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
                'class' => 'Doctrine\ORM\Mapping\Driver\XmlDriver',
                'paths' => [
                    __DIR__ . '/mappings'
                ]
            ],
            'orm_default' =>[
                'drivers' => [
                    // singular entries to let User be optional
                    'MyBackend\Entity\Role'         => __NAMESPACE__,
                    'MyBackend\Entity\Permission'   => __NAMESPACE__,
                    'MyBackend\Entity\AbstractUser' => __NAMESPACE__,
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
                        'controller' => 'MyBackend\Controller\AdminController',
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
                    ],
                    'logout' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'action' => 'logout'
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
                            'controller' => 'MyBackend\Controller\UserConsoleController',
                            'action' => 'create',
                        ],
                    ],
                ],
                'user-delete' => [
                    'options' => [
                        'route' => 'user delete [--username=] [--email=]',
                        'defaults' => [
                            'controller' => 'MyBackend\Controller\UserConsoleController',
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
            'zfcuser_user_service'                      => 'MyBackend\Service\UserService',
            'MyBackend\Listener\LoginListener'          => 'MyBackend\Listener\LoginListener',
            'MyBackend\Listener\RenderListener'         => 'MyBackend\Listener\RenderListener',
            'MyBackend\Listener\RouteListener'          => 'MyBackend\Listener\RouteListener',
        ],
        'factories' => [
            'MyBackend\Options\ModuleOptions'           => 'MyBackend\Options\ModuleOptionsFactory',
            'zfcuser_user_mapper'                       => 'MyBackend\Mapper\Doctrine\DoctrineUserMapperFactory',
            'MyBackend\Mapper\RoleMapper'               => 'MyBackend\Mapper\Doctrine\DoctrineRoleMapperFactory',
            'MyBackend\Navigation\BackendNavigation'    => 'MyBackend\Navigation\BackendNavigationFactory',
            'MyBackend\Navigation\BackendBreadcrumbs'   => 'MyBackend\Navigation\BackendBreadcrumbsFactory',
            'MyBackend\Listener\UnauthorizedListener'   => 'MyBackend\Listener\UnauthorizedListenerFactory'
        ],
        'aliases' => [
            'MyBackend\Service\UserService'             => 'zfcuser_user_service',
            'MyBackend\Mapper\UserMapper'               => 'zfcuser_user_mapper',

            // this is needed by ZfcRbac
            'Zend\Authentication\AuthenticationService' => 'zfcuser_auth_service',
        ],
        'initializers' => [
            '\MyBackend\Options\ModuleOptionsAwareInitializer',
        ],
    ],

    'controllers' => [
        'invokables' => [
            'MyBackend\Controller\AdminController' => 'MyBackend\Controller\AdminController',
            'MyBackend\Controller\UserConsoleController' => 'MyBackend\Controller\UserConsoleController',
        ],
    ],

    'controller_plugins' => [
        'factories' => [
            'MyBackend\Controller\Plugin\LoginPlugin' => 'MyBackend\Controller\Plugin\LoginPluginFactory'
        ],
        'aliases' => [
            'login' => 'MyBackend\Controller\Plugin\LoginPlugin',
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
