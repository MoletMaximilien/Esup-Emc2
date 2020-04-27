<?php

namespace Application;

use Application\Controller\GestionController;
use Application\Controller\IndexController;
use Application\Form\ModifierDescription\ModifierDescriptionForm;
use Application\Form\ModifierDescription\ModifierDescriptionFormFactory;
use Application\Form\ModifierDescription\ModifierDescriptionHydrator;
use Application\Form\ModifierLibelle\ModifierLibelleForm;
use Application\Form\ModifierLibelle\ModifierLibelleFormFactory;
use Application\Form\ModifierLibelle\ModifierLibelleHydrator;
use Application\Provider\Privilege\AdministrationPrivileges;
use Application\Provider\Privilege\RessourceRhPrivileges;
use Application\View\Helper\ActionIconViewHelper;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use UnicaenPrivilege\Guard\PrivilegeController;
use UnicaenUtilisateur\Service\Role\RoleService;
use UnicaenUtilisateur\Service\Role\RoleServiceFactory;
use Zend\Router\Http\Literal;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => IndexController::class,
                    'action' => [

                        'index-ressources',
                    ],
                    'privileges' => [
                        RessourceRhPrivileges::AFFICHER,
                    ],
                ],
                [
                    'controller' => IndexController::class,
                    'action' => [
                        'index-gestion',
                        'index-administration',
                    ],
                    'privileges' => [
                        AdministrationPrivileges::ADMINISTRATION_AFFICHER,
                    ],
                ],
            ],
        ],
    ],

    'doctrine' => [
        'driver' => [
            'orm_default' => [
                'class' => MappingDriverChain::class,
                'drivers' => [
                    'Application\Entity\Db' => 'orm_default_xml_driver',
                ],
            ],
            'orm_default_xml_driver' => [
                'class' => XmlDriver::class,
                'cache' => 'apc',
                'paths' => [
                    __DIR__ . '/../src/Application/Entity/Db/Mapping',
                ],
            ],
        ],
        'cache' => [
            'apc' => [
                'namespace' => 'PREECOG__' . __NAMESPACE__,
            ],
        ],
    ],

    'navigation' => [
        'default' => [
            'home' => [
                'pages' => [
                    'ressource' => [
                        'order' => 500,
                        'label' => 'Ressources',
                        'title' => "Ressources",
                        'route' => 'ressource-rh',
                        'resource' =>  RessourceRhPrivileges::getResourceId(RessourceRhPrivileges::AFFICHER) ,
                    ],
                    'gestion' => [
                        'order' => 400,
                        'label' => 'Gestion',
                        'title' => "Gestion des fiches, entretiens et des affectations",
                        'route' => 'gestion',
                        'resource' => AdministrationPrivileges::getResourceId(AdministrationPrivileges::ADMINISTRATION_AFFICHER)
                    ],
                    'administration' => [
                        'order' => 1000,
                        'label' => 'Administration',
                        'title' => "Administration",
                        'route' => 'administration',
                        'resource' =>  AdministrationPrivileges::getResourceId(AdministrationPrivileges::ADMINISTRATION_AFFICHER) ,
                    ],
                ],
            ],
        ],
    ],

    'router'          => [
        'routes' => [
            'home'        => [
                'type'          => Literal::class,
                'may_terminate' => true,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index', // <-- change here
                        'action'     => 'index',
                    ],
                ],
            ],
            'index-personnel' => [
                'type'          => Literal::class,
                'may_terminate' => true,
                'options' => [
                    'route'    => '/index-personnel',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index', // <-- change here
                        'action'     => 'index-personnel',
                    ],
                ],
            ],
            'index-validateur' => [
                'type'          => Literal::class,
                'may_terminate' => true,
                'options' => [
                    'route'    => '/index-validateur',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index', // <-- change here
                        'action'     => 'index-validateur',
                    ],
                ],
            ],
            'gestion'        => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/gestion',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index', // <-- change here
                        'action'     => 'index-gestion',
                    ],
                ],
                'may_terminate' => true,
            ],
            'ressource-rh'        => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/ressource-rh',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index', // <-- change here
                        'action'     => 'index-ressources',
                    ],
                ],
                'may_terminate' => true,
            ],
            'administration'        => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/administration',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index', // <-- change here
                        'action'     => 'index-administration',
                    ],
                ],
                'may_terminate' => true,
            ],
        ],
    ],
    'service_manager' => [
        'invokables' => [
        ],
        'factories' => [
            RoleService::class => RoleServiceFactory::class,
        ],
    ],
    'controllers'     => [
        'invokables' => [
        ],
        'factories' => [
            'Application\Controller\Index' => Controller\IndexControllerFactory::class,
        ]
    ],

    'form_elements' => [
        'factories' => [
            ModifierLibelleForm::class => ModifierLibelleFormFactory::class,
            ModifierDescriptionForm::class => ModifierDescriptionFormFactory::class,
        ],
    ],
    'hydrators' => [
        'invokables' => [
            ModifierLibelleHydrator::class => ModifierLibelleHydrator::class,
            ModifierDescriptionHydrator::class => ModifierDescriptionHydrator::class,
        ],
    ],

    'view_helpers' => [
        'invokables' => [
            'actionIcon' => ActionIconViewHelper::class,
        ],
    ],

    'view_manager'    => [
        'template_map'             => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],

    ],

    'translator'      => [
        'locale'                    => 'fr_FR', // en_US
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],

    'public_files' => [
        'inline_scripts' => [
//            '100_' => 'js/jquery.ui.datepicker-fr.js',
            '110_' => 'vendor/DataTables-1.10.18/datatables.min.js',
//            '112_' => 'vendor/font-awesome-5.0.9/fontawesome-all.min.js',
            '114_' => 'vendor/bootstrap-select-1.13.2/dist/js/bootstrap-select.min.js',
            '150_' => 'js/tinymce/js/tinymce/tinymce.js',
            '151_' => 'js/form_fiche.js',
            '201_' => 'vendor/chart-2.9.3/Chart.bundle.js',
//            '202_' => 'vendor/chart-2.9.3/Chart.bundle.min.js',
        ],
        'stylesheets' => [
            '050_bootstrap-theme' => '',
            '110_' => 'vendor/DataTables-1.10.18/datatables.min.css',
            '114_' => 'vendor/bootstrap-select-1.13.2/dist/css/bootstrap-select.min.css',
            '999_' => 'css/icon.css',
        ],
        'images' => [
            '100_' => 'img/PrEECoG.svg',
        ]
    ],
];
