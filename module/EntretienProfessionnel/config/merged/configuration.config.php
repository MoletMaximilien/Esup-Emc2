<?php

namespace EntretienProfessionnel;

use EntretienProfessionnel\Controller\ConfigurationController;
use EntretienProfessionnel\Controller\ConfigurationControllerFactory;
use EntretienProfessionnel\Form\ConfigurationRecopie\ConfigurationRecopieForm;
use EntretienProfessionnel\Form\ConfigurationRecopie\ConfigurationRecopieFormFactory;
use EntretienProfessionnel\Form\ConfigurationRecopie\ConfigurationRecopieHydrator;
use EntretienProfessionnel\Form\ConfigurationRecopie\ConfigurationRecopieHydratorFactory;
use EntretienProfessionnel\Provider\Privilege\EntretienproPrivileges;
use UnicaenPrivilege\Guard\PrivilegeController;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => ConfigurationController::class,
                    'action' => [
                        'ajouter-recopie',
                        'modifier-recopie',
                        'supprimer-recopie',
                    ],
                    'privileges' => [
                        EntretienproPrivileges::ENTRETIENPRO_DETRUIRE
                    ],
                ],
            ],
        ],
    ],

    'router'          => [
        'routes' => [
            'configuration' => [
//                'type'  => Literal::class,
//                'options' => [
//                    'route'    => '/configuration',
//                    'defaults' => [
//                        'controller' => ConfigurationController::class,
//                        'action'     => 'index',
//                    ],
//                ],
//                'may_terminate' => true,
                'child_routes' => [
                    /** configuration des entretien pro ***************************************************************/
                    'ajouter-recopie' => [
                        'type'  => Literal::class,
                        'options' => [
                            'route'    => '/ajouter-recopie',
                            'defaults' => [
                                'controller' => ConfigurationController::class,
                                'action'     => 'ajouter-recopie',
                            ],
                        ],
                    ],
                    'modifier-recopie' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/modifier-recopie/:recopie',
                            'defaults' => [
                                'controller' => ConfigurationController::class,
                                'action'     => 'modifier-recopie',
                            ],
                        ],
                    ],
                    'supprimer-recopie' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/supprimer-recopie/:recopie',
                            'defaults' => [
                                'controller' => ConfigurationController::class,
                                'action'     => 'supprimer-recopie',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [],
    ],
    'controllers'     => [
        'factories' => [
            ConfigurationController::class => ConfigurationControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            ConfigurationRecopieForm::class => ConfigurationRecopieFormFactory::class,
        ],
    ],
    'hydrators' => [
        'factories' => [
            ConfigurationRecopieHydrator::class => ConfigurationRecopieHydratorFactory::class,
        ],
    ]

];