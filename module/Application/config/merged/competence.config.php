<?php

namespace Application;

use Application\Controller\CompetenceController;
use Application\Controller\CompetenceControllerFactory;
use Application\Entity\Db\Competence;
use Application\Form\AgentCompetence\AgentCompetenceForm;
use Application\Form\AgentCompetence\AgentCompetenceFormFactory;
use Application\Form\AgentCompetence\AgentCompetenceHydrator;
use Application\Form\AgentCompetence\AgentCompetenceHydratorFactory;
use Application\Form\Competence\CompetenceForm;
use Application\Form\Competence\CompetenceFormFactory;
use Application\Form\Competence\CompetenceHydrator;
use Application\Form\Competence\CompetenceHydratorFactory;
use Application\Form\CompetenceTheme\CompetenceThemeForm;
use Application\Form\CompetenceTheme\CompetenceThemeFormFactory;
use Application\Form\CompetenceTheme\CompetenceThemeHydrator;
use Application\Form\CompetenceTheme\CompetenceThemeHydratorFactory;
use Application\Form\CompetenceType\CompetenceTypeForm;
use Application\Form\CompetenceType\CompetenceTypeFormFactory;
use Application\Form\CompetenceType\CompetenceTypeHydrator;
use Application\Form\CompetenceType\CompetenceTypeHydratorFactory;
use Application\Provider\Privilege\CompetencePrivileges;
use Application\Service\Competence\CompetenceService;
use Application\Service\Competence\CompetenceServiceFactory;
use UnicaenAuth\Guard\PrivilegeController;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => CompetenceController::class,
                    'action' => [
                        'index',
                        'afficher',
                        'afficher-competence-type',
                        'afficher-competence-theme',
                    ],
                    'privileges' => [
                        CompetencePrivileges::AFFICHER,
                    ],
                ],
                [
                    'controller' => CompetenceController::class,
                    'action' => [
                        'ajouter',
                        'ajouter-competence-type',
                        'ajouter-competence-theme',
                    ],
                    'privileges' => [
                        CompetencePrivileges::AJOUTER,
                    ],
                ],
                [
                    'controller' => CompetenceController::class,
                    'action' => [
                        'modifier',
                        'historiser',
                        'restaurer',
                        'modifier-competence-type',
                        'historiser-competence-type',
                        'restaurer-competence-type',
                        'modifier-competence-theme',
                        'historiser-competence-theme',
                        'restaurer-competence-theme',
                    ],
                    'privileges' => [
                        CompetencePrivileges::EDITER,
                    ],
                ],
                [
                    'controller' => CompetenceController::class,
                    'action' => [
                        'detruire',
                        'detruire-competence-type',
                        'detruire-competence-theme',
                    ],
                    'privileges' => [
                        CompetencePrivileges::EFFACER,
                    ],
                ],
            ],
        ],
    ],

    'router'          => [
        'routes' => [
            'competence' => [
                'type'  => Literal::class,
                'options' => [
                    'route'    => '/competence',
                    'defaults' => [
                        'controller' => CompetenceController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'ajouter' => [
                        'type'  => Literal::class,
                        'options' => [
                            'route'    => '/ajouter',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'ajouter',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'afficher' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/afficher/:competence',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'afficher',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/modifier/:competence',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'modifier',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'historiser' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/historiser/:competence',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'historiser',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'restaurer' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/restaurer/:competence',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'restaurer',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'detruire' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/detruire/:competence',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'detruire',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                ],
            ],
            'competence-theme' => [
                'type'  => Literal::class,
                'options' => [
                    'route'    => '/competence-theme',
                    'defaults' => [
                        'controller' => CompetenceController::class,
                    ],
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'ajouter' => [
                        'type'  => Literal::class,
                        'options' => [
                            'route'    => '/ajouter',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'ajouter-competence-theme',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'afficher' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/afficher/:competence-theme',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'afficher-competence-theme',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/modifier/:competence-theme',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'modifier-competence-theme',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'historiser' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/historiser/:competence-theme',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'historiser-competence-theme',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'restaurer' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/restaurer/:competence-theme',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'restaurer-competence-theme',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'detruire' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/detruire/:competence-theme',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'detruire-competence-theme',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                ],
            ],
            'competence-type' => [
                'type'  => Literal::class,
                'options' => [
                    'route'    => '/competence-type',
                    'defaults' => [
                        'controller' => CompetenceController::class,
                    ],
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'ajouter' => [
                        'type'  => Literal::class,
                        'options' => [
                            'route'    => '/ajouter',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'ajouter-competence-type',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'afficher' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/afficher/:competence-type',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'afficher-competence-type',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/modifier/:competence-type',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'modifier-competence-type',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'historiser' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/historiser/:competence-type',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'historiser-competence-type',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'restaurer' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/restaurer/:competence-type',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'restaurer-competence-type',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'detruire' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/detruire/:competence-type',
                            'defaults' => [
                                'controller' => CompetenceController::class,
                                'action'     => 'detruire-competence-type',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                ],
            ],
        ],
    ],

    '\Zend\Navigation\Navigation'      => [
        'default' => [
            'home' => [
                'pages' => [
                    'fiche-metier' => [
                        'pages' => [
                            'competence' => [
                                'label'    => 'Les compétences',
                                'route'    => 'competence',
                                'resource' => CompetencePrivileges::getResourceId(CompetencePrivileges::AFFICHER),
                                'order'    => 5000,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            CompetenceService::class => CompetenceServiceFactory::class,
        ],
    ],
    'controllers'     => [
        'factories' => [
            CompetenceController::class => CompetenceControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            CompetenceForm::class => CompetenceFormFactory::class,
            CompetenceThemeForm::class => CompetenceThemeFormFactory::class,
            CompetenceTypeForm::class => CompetenceTypeFormFactory::class,

            AgentCompetenceForm::class => AgentCompetenceFormFactory::class,
        ],
    ],
    'hydrators' => [
        'factories' => [
            CompetenceHydrator::class => CompetenceHydratorFactory::class,
            CompetenceThemeHydrator::class => CompetenceThemeHydratorFactory::class,
            CompetenceTypeHydrator::class => CompetenceTypeHydratorFactory::class,

            AgentCompetenceHydrator::class => AgentCompetenceHydratorFactory::class,
        ],
    ]

];