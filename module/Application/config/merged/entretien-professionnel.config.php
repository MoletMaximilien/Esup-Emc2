<?php

namespace Application;

use Application\Controller\EntretienProfessionnel\EntretienProfessionnelController;
use Application\Controller\EntretienProfessionnel\EntretienProfessionnelControllerFactory;
use Application\Form\EntretienProfessionnel\EntretienProfessionnelForm;
use Application\Form\EntretienProfessionnel\EntretienProfessionnelFormFactory;
use Application\Form\EntretienProfessionnel\EntretienProfessionnelHydrator;
use Application\Form\EntretienProfessionnel\EntretienProfessionnelHydratorFactory;
use Application\Provider\Privilege\ApplicationPrivileges;
use Application\Service\EntretienProfessionnel\EntretienProfessionnelService;
use Application\Service\EntretienProfessionnel\EntretienProfessionnelServiceFactory;
use UnicaenAuth\Guard\PrivilegeController;
use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\Http\Segment;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => EntretienProfessionnelController::class,
                    'action' => [
                        'index',
                        'creer',
                        'modifier',
                        'historiser',
                        'restaurer',
                        'detruire',
                    ],
                    'privileges' => [
                        ApplicationPrivileges::AFFICHER,
                    ],
                ],
            ],
        ],
    ],

    'router'          => [
        'routes' => [
            'entretien-professionnel' => [
                'type'  => Literal::class,
                'may_terminate' => true,
                'options' => [
                    'route'    => '/entretien-professionnel',
                    'defaults' => [
                        'controller' => EntretienProfessionnelController::class,
                        'action'     => 'index',
                    ],
                ],
                'child_routes' => [
                    'creer' => [
                        'type'  => Literal::class,
                        'may_terminate' => true,
                        'options' => [
                            'route'    => '/creer',
                            'defaults' => [
                                'controller' => EntretienProfessionnelController::class,
                                'action'     => 'creer',
                            ],
                        ],
                    ],
                    'modifier' => [
                        'type'  => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route'    => '/modifier/:entretien',
                            'defaults' => [
                                'controller' => EntretienProfessionnelController::class,
                                'action'     => 'modifier',
                            ],
                        ],
                    ],
                    'historiser' => [
                        'type'  => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route'    => '/historiser/:entretien',
                            'defaults' => [
                                'controller' => EntretienProfessionnelController::class,
                                'action'     => 'historiser',
                            ],
                        ],
                    ],
                    'restaurer' => [
                        'type'  => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route'    => '/restaurer/:entretien',
                            'defaults' => [
                                'controller' => EntretienProfessionnelController::class,
                                'action'     => 'restaurer',
                            ],
                        ],
                    ],
                    'detruire' => [
                        'type'  => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route'    => '/detruire/:entretien',
                            'defaults' => [
                                'controller' => EntretienProfessionnelController::class,
                                'action'     => 'detruire',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            EntretienProfessionnelService::class => EntretienProfessionnelServiceFactory::class,
        ],
    ],
    'controllers'     => [
        'factories' => [
            EntretienProfessionnelController::class => EntretienProfessionnelControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            EntretienProfessionnelForm::class => EntretienProfessionnelFormFactory::class,
        ],
    ],
    'hydrators' => [
        'factories' => [
            EntretienProfessionnelHydrator::class => EntretienProfessionnelHydratorFactory::class,
        ],
    ]

];