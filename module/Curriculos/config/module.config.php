<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Curriculos;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\Router\Http\Regex;
use Zend\ServiceManager\Factory\InvokableFactory;
use Curriculos\Route\StaticRoute;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' => [
        'routes' => [
            'curriculos' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/curriculos',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],

            'curriculosposts' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/curriculosposts[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        //'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        //'id' => '[0-9]*'
                        'id' =>'[a-zA-Z0-9-_\.]+',
                    ],
                    'defaults' => [
                        'controller'    => Controller\CurriculosController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
            
     
            
            'Curriculosadmin' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/Curriculosadmin[/:action[/:page]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\CurriculosController::class,
                        'action'        => 'admin',
                    ],
                ],
            ],
            
            'curriculosmyadmin' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/curriculosmyadmin[/:action[/:page]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\CurriculosController::class,
                        'action'        => 'myadmin',
                    ],
                ],
            ],
            
            'curriculosadminaprovar' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/curriculosadminaprovar[/:action[/:page]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\CurriculosController::class,
                        'action'        => 'adminaprovar',
                    ],
                ],
            ],
            
            
            
            'curriculosadminexpired' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/curriculosadminexpired[/:action[/:page]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\CurriculosController::class,
                        'action'        => 'adminexpired',
                    ],
                ],
            ],
            
            
            
            'curriculoscontactos' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/curriculoscontactos[/:action[/:id]]',
                     'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\CurriculosController::class,
                        'action'     => 'contactos',
                    ],
                ],
            ],
            

            
            'curriculosdeleteMessage' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/curriculosdeleteMessage[/:action[/:id][/:idanuncio]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'idanuncio' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\CurriculosController::class,
                        'action'        => 'deleteMessage',
                    ],
                ],
            ],
            'curriculosviewmessage' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/curriculosviewmessage[/:action[/:id][/:idanuncio]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'idanuncio' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\CurriculosController::class,
                        'action'        => 'viewmessage',
                    ],
                ],
            ],
            
        'curriculospesquisa' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/curriculospesquisa[/:action[/:id]]',
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
            
        'curriculospaginator-search' => [
                'type'    => Segment::class,
                'options' => [
                           'route'    => '/curriculospaginator-search[/:action[/smypage/:page][/search_by/:search_by]]',
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
            
     
        'curriculossee' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/curriculossee[/:action[/:id][/:page]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'page' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\CurriculosController::class,
                        'action'        => 'seemessages',
                    ],
                ],
            ],           
            
                                    
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,      
            Controller\CurriculosController::class => Controller\Factory\CurriculosControllerFactory::class,
           
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
            Controller\CurriculosController::class => [
                // Allow anyone to visit "index" and "about" actions
                ['actions' => ['view','contactos'], 'allow' => '*'],
                ['actions' => ['add','myadmin','edit'], 'allow' => 'm'],
                ['actions' => ['seemessages'], 'allow' => 'p'],
                ['actions' => ['admin','deletecomment','adminaprovar','adminexpired','apagarexpirados'], 'allow' => '@'],
            ],
 
            
        ]
    ],
    'service_manager' => [
        'factories' => [
     
            Service\AutoManager::class => Service\Factory\AutoManagerFactory::class,
            \Zend\I18n\Translator\TranslatorInterface::class => \Zend\I18n\Translator\TranslatorServiceFactory::class,
        
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
