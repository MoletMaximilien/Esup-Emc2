<?php

namespace Application\Service\ActivitesDescriptionsRetirees;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use UnicaenUtilisateur\Service\User\UserService;;

class ActivitesDescriptionsRetireesServiceFactory {

    /**
     * @param ContainerInterface $container
     * @return ActivitesDescriptionsRetireesService
     */
    public function __invoke(ContainerInterface $container)
    {
        /**
         * @var EntityManager $entityManager
         * @var UserService $userService
         */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $userService = $container->get(UserService::class);

        /** @var ActivitesDescriptionsRetireesService $service */
        $service = new ActivitesDescriptionsRetireesService();
        $service->setEntityManager($entityManager);
        $service->setUserService($userService);
        return $service;
    }
}