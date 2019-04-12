<?php

namespace Application;

use Application\Controller\FicheMetier\FicheMetierController;
use Application\Controller\FicheMetier\FicheMetierControllerFactory;
use Application\Controller\FicheMetier\FicheMetierTypeController;
use Application\Controller\FicheMetier\FicheMetierTypeControllerFactory;
use Application\Form\FicheMetier\AjouterFicheTypeForm;
use Application\Form\FicheMetier\AjouterFicheTypeFormFactory;
use Application\Form\FicheMetier\AjouterFicheTypeHydrator;
use Application\Form\FicheMetier\AjouterFicheTypeHydratorFactory;
use Application\Form\FicheMetier\AssocierAgentForm;
use Application\Form\FicheMetier\AssocierAgentFormFactory;
use Application\Form\FicheMetier\AssocierAgentHydrator;
use Application\Form\FicheMetier\AssocierAgentHydratorFactory;
use Application\Form\FicheMetier\AssocierMetierTypeForm;
use Application\Form\FicheMetier\AssocierMetierTypeFormFactory;
use Application\Form\FicheMetier\AssocierMetierTypeHydrator;
use Application\Form\FicheMetier\AssocierMetierTypeHydratorFactory;
use Application\Form\FicheMetier\AssocierPosteForm;
use Application\Form\FicheMetier\AssocierPosteFormFactory;
use Application\Form\FicheMetier\AssocierPosteHydrator;
use Application\Form\FicheMetier\AssocierPosteHydratorFactory;
use Application\Form\FicheMetier\FicheMetierCreationForm;
use Application\Form\FicheMetier\FicheMetierCreationFormFactory;
use Application\Form\FicheMetier\FicheMetierCreationHydrator;
use Application\Form\FicheMetier\FicheMetierCreationHydratorFactory;
use Application\Form\FicheMetier\SpecificitePosteForm;
use Application\Form\FicheMetier\SpecificitePosteFormFactory;
use Application\Form\FicheMetierType\ActiviteExistanteForm;
use Application\Form\FicheMetierType\ActiviteExistanteFormFactory;
use Application\Form\FicheMetierType\ApplicationsForm;
use Application\Form\FicheMetierType\ApplicationsFormFactory;
use Application\Form\FicheMetierType\ApplicationsHydrator;
use Application\Form\FicheMetierType\ApplicationsHydratorFactory;
use Application\Form\FicheMetierType\FormationBaseForm;
use Application\Form\FicheMetierType\FormationBaseFormFactory;
use Application\Form\FicheMetierType\FormationBaseHydrator;
use Application\Form\FicheMetierType\FormationComportementaleForm;
use Application\Form\FicheMetierType\FormationComportementaleFormFactory;
use Application\Form\FicheMetierType\FormationComportementaleHydrator;
use Application\Form\FicheMetierType\FormationOperationnelleForm;
use Application\Form\FicheMetierType\FormationOperationnelleFormFactory;
use Application\Form\FicheMetierType\FormationOperationnelleHydrator;
use Application\Form\FicheMetierType\LibelleForm;
use Application\Form\FicheMetierType\LibelleFormFactory;
use Application\Form\FicheMetierType\LibelleHydrator;
use Application\Form\FicheMetierType\LibelleHydratorFactory;
use Application\Form\FicheMetierType\MissionsPrincipalesForm;
use Application\Form\FicheMetierType\MissionsPrincipalesFormFactory;
use Application\Provider\Privilege\FicheMetierPrivileges;
use Application\Service\FicheMetier\FicheMetierService;
use Application\Service\FicheMetier\FicheMetierServiceFactory;
use Application\View\Helper\FicheTypeExterneViewHelper;
use Application\View\Helper\FicheTypeViewHelper;
use Application\View\Helper\SpecificitePosteViewHelper;
use UnicaenAuth\Guard\PrivilegeController;
use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\Http\Segment;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => FicheMetierController::class,
                    'action' => [
                        'index',
                    ],
                    'roles' => [
                    ],
                ],
                [
                    'controller' => FicheMetierController::class,
                    'action' => [
                        'afficher',
                        'editer-specificite-poste',
                        'associer-metier-type',
                        'associer-agent',
                        'associer-poste',
                        'ajouter-fiche-type',
                        'retirer-fiche-type',
                        'modifier-fiche-type',
                        'selectionner-activite',
                    ],
                    'privileges' => [
                        FicheMetierPrivileges::AFFICHER,
                    ],
                ],
                [
                    'controller' => FicheMetierController::class,
                    'action' => [
                        'historiser',
                        'restaurer',
                    ],
                    'privileges' => [
                        FicheMetierPrivileges::HISTORISER,
                    ],
                ],
                [
                    'controller' => FicheMetierController::class,
                    'action' => [
                        'editer',
                        'creer',
                    ],
                    'privileges' => [
                        FicheMetierPrivileges::EDITER,
                    ],
                ],
                [
                    'controller' => FicheMetierTypeController::class,
                    'action' => [
                        'index',
                    ],
                    'roles' => [
                    ],
                ],
                [
                    'controller' => FicheMetierTypeController::class,
                    'action' => [
                        'afficher',
                        'editer',
                        'detruire',
                        'historiser',
                        'restaurer',

                        'editer-libelle',
                        'editer-missions-principales',
                        'retirer-activite',
                        'deplacer-activite',
                        'ajouter-nouvelle-activite',
                        'ajouter-activite-existante',

                        'modifier-connaissances',
                        'modifier-operationnelle',
                        'modifier-comportementale',
                        'modifier-application',
                        'ajouter',
                    ],
                    'privileges' => [
                        FicheMetierPrivileges::AFFICHER,
                    ],
                ],
            ],
        ],
    ],

    'router'          => [
        'routes' => [
            'fiche-metier' => [
                'type'  => Literal::class,
                'options' => [
                    'route'    => '/fiche-metier',
                    'defaults' => [
                        'controller' => FicheMetierController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'afficher' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/afficher/:id',
                            'defaults' => [
                                'controller' => FicheMetierController::class,
                                'action'     => 'afficher',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'associer-poste' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/associer-poste/:fiche',
                            'defaults' => [
                                'controller' => FicheMetierController::class,
                                'action'     => 'associer-poste',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'associer-metier-type' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/associer-metier-type/:fiche',
                            'defaults' => [
                                'controller' => FicheMetierController::class,
                                'action'     => 'associer-metier-type',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'retirer-fiche-type' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/retirer-fiche-type/:fiche/:fiche-type-externe',
                            'defaults' => [
                                'controller' => FicheMetierController::class,
                                'action'     => 'retirer-fiche-type',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier-fiche-type' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/modifier-fiche-type/:fiche/:fiche-type-externe',
                            'defaults' => [
                                'controller' => FicheMetierController::class,
                                'action'     => 'modifier-fiche-type',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'selectionner-activite' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/selectionner-activite/:fiche/:fiche-type-externe',
                            'defaults' => [
                                'controller' => FicheMetierController::class,
                                'action'     => 'selectionner-activite',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'ajouter-fiche-type' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/ajouter-fiche-type/:fiche',
                            'defaults' => [
                                'controller' => FicheMetierController::class,
                                'action'     => 'ajouter-fiche-type',
                            ],
                        ],
                        'may_terminate' => true,
                    ],

                    'creer' => [
                        'type'  => Literal::class,
                        'options' => [
                            'route'    => '/creer',
                            'defaults' => [
                                'controller' => FicheMetierController::class,
                                'action'     => 'creer',
                            ],
                        ],
                    ],
                ],
            ],
            'fiche-metier-type' => [
                'type'  => Literal::class,
                'options' => [
                    'route'    => '/fiche-metier-type',
                    'defaults' => [
                        'controller' => FicheMetierTypeController::class,
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
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'ajouter',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'afficher' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/afficher/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'afficher',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'editer' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/editer/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'editer',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'historiser' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/historiser/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'historiser',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'restaurer' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/restaurer/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'restaurer',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'detruire' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/detruire/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'detruire',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'editer-libelle' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/editer-libelle/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'editer-libelle',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'editer-missions-principales' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/editer-missions-principales/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'editer-missions-principales',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'retirer-activite' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/retirer-activite/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'retirer-activite',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'deplacer-activite' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/deplacer-activite/:id/:direction',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'deplacer-activite',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'ajouter-nouvelle-activite' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/ajouter-nouvelle-activite/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'ajouter-nouvelle-activite',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'ajouter-activite-existante' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/ajouter-activite-existante/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'ajouter-activite-existante',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier-connaissances' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/modifier-connaissances/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'modifier-connaissances',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier-operationnelle' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/modifier-operationnelle/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'modifier-operationnelle',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier-comportementale' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/modifier-comportementale/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'modifier-comportementale',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier-application' => [
                        'type'  => Segment::class,
                        'options' => [
                            'route'    => '/modifier-application/:id',
                            'defaults' => [
                                'controller' => FicheMetierTypeController::class,
                                'action'     => 'modifier-application',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'invokables' => [
        ],
        'factories' => [
            FicheMetierService::class => FicheMetierServiceFactory::class,
        ],
    ],
    'controllers'     => [
        'factories' => [
            FicheMetierController::class => FicheMetierControllerFactory::class,
            FicheMetierTypeController::class => FicheMetierTypeControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'invokables' => [
        ],
        'factories' => [
            ActiviteExistanteForm::class => ActiviteExistanteFormFactory::class,
            FicheMetierCreationForm::class => FicheMetierCreationFormFactory::class,
            LibelleForm::class => LibelleFormFactory::class,
            MissionsPrincipalesForm::class => MissionsPrincipalesFormFactory::class,

            AssocierMetierTypeForm::class => AssocierMetierTypeFormFactory::class,
            AssocierPosteForm::class => AssocierPosteFormFactory::class,

            FormationBaseForm::class => FormationBaseFormFactory::class,
            FormationOperationnelleForm::class => FormationOperationnelleFormFactory::class,
            FormationComportementaleForm::class => FormationComportementaleFormFactory::class,
            ApplicationsForm::class => ApplicationsFormFactory::class,

            AjouterFicheTypeForm::class=>AjouterFicheTypeFormFactory::class,
        ],
    ],
    'hydrators' => [
        'invokables' => [
            FormationBaseHydrator::class => FormationBaseHydrator::class,
            FormationOperationnelleHydrator::class => FormationOperationnelleHydrator::class,
            FormationComportementaleHydrator::class => FormationComportementaleHydrator::class,

        ],
        'factories' => [
            FicheMetierCreationHydrator::class => FicheMetierCreationHydratorFactory::class,
            LibelleHydrator::class => LibelleHydratorFactory::class,
            AssocierMetierTypeHydrator::class => AssocierMetierTypeHydratorFactory::class,
            AssocierPosteHydrator::class => AssocierPosteHydratorFactory::class,
            ApplicationsHydrator::class => ApplicationsHydratorFactory::class,

            AjouterFicheTypeHydrator::class => AjouterFicheTypeHydratorFactory::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'specificitePoste' => SpecificitePosteViewHelper::class,
            'ficheTypeExterne' => FicheTypeExterneViewHelper::class,
            'ficheMetierType'  => FicheTypeViewHelper::class,
        ],
    ],

];