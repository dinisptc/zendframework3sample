<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Empregos;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\Router\Http\Regex;
use Zend\ServiceManager\Factory\InvokableFactory;
use Empregos\Route\StaticRoute;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' => [
        'routes' => [
            'empregos' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/empregos',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],

            'empregosposts' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/empregosposts[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        //'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        //'id' => '[0-9]*'
                        'id' =>'[a-zA-Z0-9-_\.]+',
                    ],
                    'defaults' => [
                        'controller'    => Controller\EmpregosController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
            
     
            
            'empregosadmin' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/empregosadmin[/:action[/:page]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\EmpregosController::class,
                        'action'        => 'admin',
                    ],
                ],
            ],
            
            'empregosmyadmin' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/empregosmyadmin[/:action[/:page]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\EmpregosController::class,
                        'action'        => 'myadmin',
                    ],
                ],
            ],
            
            'empregosadminaprovar' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/empregosadminaprovar[/:action[/:page]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\EmpregosController::class,
                        'action'        => 'adminaprovar',
                    ],
                ],
            ],
            
            
            
            'empregosadminexpired' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/empregosadminexpired[/:action[/:page]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\EmpregosController::class,
                        'action'        => 'adminexpired',
                    ],
                ],
            ],
            
            
            
            'empregoscontactos' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/empregoscontactos[/:action[/:id]]',
                     'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\EmpregosController::class,
                        'action'     => 'contactos',
                    ],
                ],
            ],
            
            'empregosimages' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/empregosimages[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\ImageController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
            
            'empregosdeleteMessage' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/empregosdeleteMessage[/:action[/:id][/:idanuncio]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'idanuncio' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\EmpregosController::class,
                        'action'        => 'deleteMessage',
                    ],
                ],
            ],
            'empregosviewmessage' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/empregosviewmessage[/:action[/:id][/:idanuncio]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'idanuncio' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\EmpregosController::class,
                        'action'        => 'viewmessage',
                    ],
                ],
            ],
            
        'empregospesquisa' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/empregospesquisa[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\IndexController::class,
                        'action'        => 'pesquisa',
                    ],
                ],
            ],
            
        'empregospaginator-search' => [
                'type'    => Segment::class,
                'options' => [
                           'route'    => '/empregospaginator-search[/:action[/smypage/:page][/search_by/:search_by]]',
                   // 'route'    => '/paginator-search/[smypage/:page][/search_by/:search_by]',
                    'constraints' => [
                             'page' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\IndexController::class,
                        'action'        => 'pesquisa',
                    ],
                ],
            ],
            
     
        'empregossee' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/empregossee[/:action[/:id][/:page]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'page' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\EmpregosController::class,
                        'action'        => 'seemessages',
                    ],
                ],
            ],           
            
                                    
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,      
            Controller\EmpregosController::class => Controller\Factory\EmpregosControllerFactory::class,
            Controller\ImageController::class => Controller\Factory\ImageControllerFactory::class,
        ],
    ],
        // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'options' => [
            // The access filter can work in 'restrictive' (recommended) or 'permissive'
            // mode. In restrictive mode all controller actions must be explicitly listed 
            // under the 'access_filter' config key, and access is denied to any not listed 
            // action for not logged in users. In permissive mode, if an action is not listed 
            // under the 'access_filter' key, access to it is permitted to anyone (even for 
            // not logged in users. Restrictive mode is more secure and recommended to use.
            'mode' => 'restrictive'
        ],
        'controllers' => [
            Controller\IndexController::class => [
                // Allow anyone to visit "index" and "about" actions
                ['actions' => ['index','pesquisa'], 'allow' => '*'],
               
            ],
            Controller\EmpregosController::class => [
                // Allow anyone to visit "index" and "about" actions
                ['actions' => ['view','contactos'], 'allow' => '*'],
                ['actions' => ['add','myadmin','edit'], 'allow' => 'm'],
                ['actions' => ['seemessages'], 'allow' => 'p'],
                ['actions' => ['admin','deletecomment','adminaprovar','adminexpired','apagarexpirados'], 'allow' => '@'],
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
     
            Service\AutoManager::class => Service\Factory\AutoManagerFactory::class,
            \Zend\I18n\Translator\TranslatorInterface::class => \Zend\I18n\Translator\TranslatorServiceFactory::class,
             Service\ImageManager::class => InvokableFactory::class,
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
    // The following registers our custom view 
    // helper classes in view plugin manager.
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
];
