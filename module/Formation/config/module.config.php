<?php

namespace Formation;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Formation\Event\Convocation\ConvocationEvent;
use Formation\Event\Convocation\ConvocationEventFactory;
use Formation\Event\InscriptionCloture\InscriptionClotureEvent;
use Formation\Event\InscriptionCloture\InscriptionClotureEventFactory;
use Formation\Provider\Privilege\FormationPrivileges;
use Formation\Service\Evenement\NotificationFormationsOuvertesService;
use Formation\Service\Evenement\NotificationFormationsOuvertesServiceFactory;
use Formation\Service\Evenement\RappelAgentAvantFormationService;
use Formation\Service\Evenement\RappelAgentAvantFormationServiceFactory;
use Formation\Service\Notification\NotificationService;
use Formation\Service\Notification\NotificationServiceFactory;
use Formation\Service\Url\UrlService;
use Formation\Service\Url\UrlServiceFactory;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
            ],
        ],
    ],

    'doctrine' => [
        'driver' => [
            'orm_default' => [
                'class' => MappingDriverChain::class,
                'drivers' => [
                    'Formation\Entity\Db' => 'orm_default_xml_driver',
                ],
            ],
            'orm_default_xml_driver' => [
                'class' => XmlDriver::class,
                'cache' => 'apc',
                'paths' => [
                    __DIR__ . '/../src/Formation/Entity/Db/Mapping',
                ],
            ],
        ],
        'cache' => [
            'apc' => [
                'namespace' => 'PREECOG__Formation__' . __NAMESPACE__,
            ],
        ],
    ],


    'navigation'      => [
        'default' => [
            'home' => [
                'pages' => [
                    'ressource' => [
                        'pages' => [
                            'formation' => [
                                'label'    => 'Gestion des formations',
                                'route'    => 'formation',
                                'resource' => FormationPrivileges::getResourceId(FormationPrivileges::FORMATION_ACCES),
                                'order'    => 300,
                                'dropdown-header' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            NotificationService::class => NotificationServiceFactory::class,
            UrlService::class => UrlServiceFactory::class,

            //Evenement
            NotificationFormationsOuvertesService::class => NotificationFormationsOuvertesServiceFactory::class,
            RappelAgentAvantFormationService::class => RappelAgentAvantFormationServiceFactory::class,
            InscriptionClotureEvent::class => InscriptionClotureEventFactory::class,
            ConvocationEvent::class => ConvocationEventFactory::class,
        ],
    ],
    'controllers'     => [
        'factories' => [
        ]
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
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

];
