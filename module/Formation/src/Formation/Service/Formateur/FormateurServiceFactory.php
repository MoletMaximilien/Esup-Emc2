<?php

namespace Formation\Service\Formateur;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class FormateurServiceFactory
{

    /**
     * @param ContainerInterface $container
     * @return FormateurService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container) : FormateurService
    {
        /**
         * @var EntityManager $entityManager
         */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        $service = new FormateurService();
        $service->setObjectManager($entityManager);
        return $service;
    }
}