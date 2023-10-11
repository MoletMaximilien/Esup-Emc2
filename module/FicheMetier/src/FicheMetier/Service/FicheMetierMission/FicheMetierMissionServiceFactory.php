<?php

namespace FicheMetier\Service\FicheMetierMission;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class FicheMetierMissionServiceFactory
{

    /**
     * @param ContainerInterface $container
     * @return FicheMetierMissionService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): FicheMetierMissionService
    {
        /**
         * @var EntityManager $entityManager
         */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        $service = new FicheMetierMissionService();
        $service->setEntityManager($entityManager);
        return $service;
    }
}