<?php

namespace EntretienProfessionnel\Service\Evenement;

use Doctrine\ORM\EntityManager;
use EntretienProfessionnel\Service\EntretienProfessionnel\EntretienProfessionnelService;
use EntretienProfessionnel\Service\Notification\NotificationService;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use UnicaenEvenement\Service\Etat\EtatService;
use UnicaenEvenement\Service\Type\TypeService;

class RappelEntretienProfessionnelServiceFactory {

    /**
     * @param ContainerInterface $container
     * @return RappelEntretienProfessionnelService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container) : RappelEntretienProfessionnelService
    {
        /**
         * @var EntityManager $entityManager
         * @var EntretienProfessionnelService $entretienProfessionnelService
         * @var EtatService $etatService
         * @var NotificationService $notificationService
         * @var TypeService $typeService
         */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $entretienProfessionnelService = $container->get(EntretienProfessionnelService::class);
        $etatService = $container->get(EtatService::class);
        $notificationService = $container->get(NotificationService::class);
        $typeService = $container->get(TypeService::class);

        $service = new RappelEntretienProfessionnelService();

        $service->setObjectManager($entityManager);
        $service->setEntretienProfessionnelService($entretienProfessionnelService);
        $service->setEtatEvenementService($etatService);
        $service->setNotificationService($notificationService);
        $service->setTypeService($typeService);
        return $service;
    }
}