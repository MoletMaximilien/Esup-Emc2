<?php

namespace Autoform\Service\Formulaire;

use Utilisateur\Service\User\UserService;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormulaireReponseServiceFactory {

    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        /**
         * @var EntityManager $entityManager
         * @var UserService $userService
         */
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $userService = $serviceLocator->get(UserService::class);

        /** @var FormulaireReponseService $service */
        $service = new FormulaireReponseService();
        $service->setEntityManager($entityManager);
        $service->setUserService($userService);
        return $service;
    }
}