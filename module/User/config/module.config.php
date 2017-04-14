<?php
namespace User;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' => [
        'routes' => [
            'login' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
            'reset-password' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/reset-password',
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action'     => 'resetPassword',
                    ],
                ],
            ],
            'users' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/users[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        //'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\UserController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
            'set-password' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/set-password[/:token]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        //'token' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\UserController::class,
                        'action'        => 'setPassword',
                    ],
                ],
            ],
            'register' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/register',
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action'     => 'register',
                    ],
                ],
            ],
            'set-register' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/set-register[/:token]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        //'token' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\UserController::class,
                        'action'        => 'setRegister',
                    ],
                ],
            ],
            'unsubscribe' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/unsubscribe[/:action[/:id]]',
                     'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                       //'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action'     => 'unsubscribe',
                    ],
                ],
            ],
            
            'subscribe' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/subscribe[/:action[/:id]]',
                     'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        //'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action'     => 'subscribe',
                    ],
                ],
            ],
            
            'userimages' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/userimages[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        //'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\ImageController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
            
            'empresas' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/empresas[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        //'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\EmpresaController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\AuthController::class => Controller\Factory\AuthControllerFactory::class,
            Controller\UserController::class => Controller\Factory\UserControllerFactory::class,   
            Controller\ImageController::class => Controller\Factory\ImageControllerFactory::class,
            Controller\EmpresaController::class => Controller\Factory\EmpresaControllerFactory::class,   
            Controller\EmpresaImageController::class => Controller\Factory\EmpresaImageControllerFactory::class,
        ],
    ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'controllers' => [
            Controller\UserController::class => [
                // Give access to "resetPassword", "message" and "setPassword" actions
                // to anyone.
                ['actions' => ['resetPassword', 'message', 'messageregistration', 'setPassword', 'register', 'setRegister','unsubscribe'], 'allow' => '*'],
                // Give access to "index", "add", "edit", "view", "changePassword" actions to authorized users only.
                ['actions' => ['changePassword'], 'allow' => 'm'],
                 ['actions' => ['changePassword'], 'allow' => 'p'],
                ['actions' => ['index', 'add', 'edit', 'view', 'changePasswordadmin'], 'allow' => '@']
            ],
            Controller\ImageController::class => [
                // Allow anyone to visit "index" and "about" actions
                ['actions' => ['file'], 'allow' => '*'],
                ['actions' => ['index','upload'], 'allow' => 'm'],
               
            ],
        ]
    ],
    'service_manager' => [
        'factories' => [
            \Zend\Authentication\AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\UserManager::class => Service\Factory\UserManagerFactory::class,
             \Zend\I18n\Translator\TranslatorInterface::class => \Zend\I18n\Translator\TranslatorServiceFactory::class,
            Service\ImageManager::class => Service\Factory\ImageManagerFactory::class,
        ],
        'aliases' => [
     
            'translator' => 'MvcTranslator',
        ],
    ],
        'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
                //'text_domain' => __NAMESPACE__,
            ],
        ],
    ],
        'view_helpers' => [
   
        'invokables' => [
             'translate' => \Zend\I18n\View\Helper\Translate::class
        ],
   
    ],
    
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
    
            // The following key allows to define custom styling for FlashMessenger view helper.
    'view_helper_config' => [
        'flashmessenger' => [
            'message_open_format'      => '<div%s><ul><li>',
            'message_close_string'     => '</li></ul></div>',
            'message_separator_string' => '</li><li>'
        ]
    ],  
];
