<?php

namespace Application;

use Application\Controller\Structure\StructureController;
use Application\Controller\Structure\StructureControllerFactory;
use Application\Form\Structure\StructureForm;
use Application\Form\Structure\StructureFormFactory;
use Application\Form\Structure\StructureHydrator;
use Application\Provider\Privilege\StructurePrivileges;
use Application\Service\Structure\StructureService;
use Application\Service\Structure\StructureServiceFactory;
use UnicaenAuth\Guard\PrivilegeController;
use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\Http\Segment;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => StructureController::class,
                    'action' => [
                        'index',
                    ],
                    'privileges' => [
                        StructurePrivileges::AFFICHER,
                    ],
                ],
                [
                    'controller' => StructureController::class,
                    'action' => [
                        'ajouter-gestionnaire',
                        'retirer-gestionnaire',
                    ],
                    'privileges' => [
                        StructurePrivileges::GESTIONNAIRE,
                    ],
                ],
                [
                    'controller' => StructureController::class,
                    'action' => [
                        'editer-description'
                    ],
                    'privileges' => [
                        StructurePrivileges::EDITER,
                    ],
                ],
            ],
        ],
    ],

    'router'          => [
        'routes' => [
            'structure' => [
                'type'  => Literal::class,
                'options' => [
                    'route'    => '/structure',
                    'defaults' => [
                        'controller' => StructureController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'ajouter-gestionnaire' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/ajouter-gestionnaire/:structure',
                            'defaults' => [
                                'controller' => StructureController::class,
                                'action'     => 'ajouter-gestionnaire',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [],
                    ],
                    'retirer-gestionnaire' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/retirer-gestionnaire/:structure',
                            'defaults' => [
                                'controller' => StructureController::class,
                                'action'     => 'retirer-gestionnaire',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [],
                    ],
                    'editer-description' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/editer-description/:structure',
                            'defaults' => [
                                'controller' => StructureController::class,
                                'action'     => 'editer-description',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [],
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            StructureService::class => StructureServiceFactory::class,
        ],
    ],
    'controllers'     => [
        'factories' => [
            StructureController::class => StructureControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            StructureForm::class => StructureFormFactory::class,
        ],
    ],
    'hydrators' => [
        'invokables' => [
            StructureHydrator::class => StructureHydrator::class,
        ],
    ]

];