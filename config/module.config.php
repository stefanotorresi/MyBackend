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

    /**
     * ZfcRbac module
     */
    'zfc_rbac' => [
        'protection_policy' => 'allow',
        'guards' => [
            'ZfcRbac\Guard\RouteGuard' => [
                'admin/login'=> ['guest'],
                'admin*' => ['admin'],
            ],
        ],
        'role_providers' => [
            'ZfcRbac\Role\ObjectRepositoryRoleProvider' => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'class_name'     => 'MyBackend\Entity\Role',
            ],
        ],
        'permission_providers' => [
            'ZfcRbac\Permission\ObjectRepositoryPermissionProvider' => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'class_name'     => 'MyBackend\Entity\Permission',
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
                        'route' => 'user create-admin [--username=] [--email=]',
                        'defaults' => [
                            'controller' => __NAMESPACE__ . '\Controller\UserConsoleController',
                            'action' => 'create-admin',
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
        'factories' => [
            'MyBackend\Options\ModuleOptions'   => 'MyBackend\Options\ModuleOptionsFactory',
            'zfcuser_user_mapper'               => 'MyBackend\Mapper\UserMapperFactory',
            'nav-backend'                       => 'MyBackend\Service\BackendNavigationFactory',
        ],
        'aliases' => [
            'MyBackend\Mapper\UserMapper' => 'zfcuser_user_mapper',

            // this is needed by ZfcRbac
            'Zend\Authentication\AuthenticationService' => 'zfcuser_auth_service',

            // these are needed by ZfcUser
            'zfcuser_zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
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

    'asset_manager' => [
        'resolver_configs' => [
            'collections' => [
                'css/backend.css' => [
                    'sass/backend.scss',
                ],
                'js/backend-scripts.js' => [
                    'js/jquery-bundle.js',
                    'js/bootstrap.js',
                    'js/application.js',
                    'js/backend.js',
                ],
                /**
                 * note: missing configuration is assumed to be provided by stefanotorresi/MySkeleton
                 * @link https://github.com/stefanotorresi/MySkeleton/blob/1.0.1-beta/config/autoload/global.php
                 */
            ],
            'map' => [
                'sass/backend.scss' => __DIR__ . '/../assets/sass/backend.scss',
                'sass/backend.login.scss' => __DIR__ . '/../assets/sass/backend.login.scss',
                'js/backend.js' => __DIR__ . '/../assets/js/backend.js',
            ],
        ],
        'filters' => [
            'css/backend.css' => [
                [ 'service' => 'MyAsseticSassFilter' ],
            ],
        ],
    ],
];
