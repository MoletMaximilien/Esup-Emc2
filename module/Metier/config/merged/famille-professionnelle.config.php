<?php

namespace Metier;

use Metier\Controller\FamilleProfessionnelleController;
use Metier\Controller\FamilleProfessionnelleControllerFactory;
use Metier\Provider\Privilege\FamilleprofessionnellePrivileges;
use Metier\Service\FamilleProfessionnelle\FamilleProfessionnelleService;
use Metier\Service\FamilleProfessionnelle\FamilleProfessionnelleServiceFactory;
use UnicaenPrivilege\Guard\PrivilegeController;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => FamilleProfessionnelleController::class,
                    'action' => [
                        'ajouter',
                    ],
                    'privileges' => [
                        FamilleprofessionnellePrivileges::FAMILLE_PROFESSIONNELLE_AJOUTER
                    ],
                ],
                [
                    'controller' => FamilleProfessionnelleController::class,
                    'action' => [
                        'modifier',
                    ],
                    'privileges' => [
                        FamilleprofessionnellePrivileges::FAMILLE_PROFESSIONNELLE_MODIFIER
                    ],
                ],
                [
                    'controller' => FamilleProfessionnelleController::class,
                    'action' => [
                        'historiser',
                        'restaurer',
                    ],
                    'privileges' => [
                        FamilleprofessionnellePrivileges::FAMILLE_PROFESSIONNELLE_HISTORISER
                    ],
                ],
                [
                    'controller' => FamilleProfessionnelleController::class,
                    'action' => [
                        'effacer',
                    ],
                    'privileges' => [
                        FamilleprofessionnellePrivileges::FAMILLE_PROFESSIONNELLE_SUPPRIMER
                    ],
                ],
            ],
        ],
    ],

    'router'          => [
        'routes' => [
            'famille-professionnelle' => [
                'type'  => Literal::class,
                'options' => [
                    'route'    => '/famille-professionnelle',
                    'defaults' => [
                        'controller' => FamilleProfessionnelleController::class,
                    ],
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'ajouter' => [
                        'type'  => Literal::class,
                        'options' => [
                            'route'    => '/ajouter',
                            'defaults' => [
                                'controller' => FamilleProfessionnelleController::class,
                                'action'     => 'ajouter',
                            ],
                        ],
                    ],
                    'modifier' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/modifier/:famille-professionnelle',
                            'defaults' => [
                                'controller' => FamilleProfessionnelleController::class,
                                'action'     => 'modifier',
                            ],
                        ],
                    ],
                    'historiser' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/historiser-famille/:famille-professionnelle',
                            'defaults' => [
                                'controller' => FamilleProfessionnelleController::class,
                                'action'     => 'historiser',
                            ],
                        ],
                    ],
                    'restaurer' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/restaurer-famille/:famille-professionnelle',
                            'defaults' => [
                                'controller' => FamilleProfessionnelleController::class,
                                'action'     => 'restaurer',
                            ],
                        ],
                    ],
                    'effacer' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/effacer-famille/:famille-professionnelle',
                            'defaults' => [
                                'controller' => FamilleProfessionnelleController::class,
                                'action'     => 'effacer',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            FamilleProfessionnelleService::class => FamilleProfessionnelleServiceFactory::class,
        ],
    ],
    'controllers'     => [
        'factories' => [
            FamilleProfessionnelleController::class => FamilleProfessionnelleControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
        ],
    ],
    'hydrators' => [
        'factories' => [
        ],
    ],
    'view_helpers' => [
        'invokables' => [
        ],
    ],
];