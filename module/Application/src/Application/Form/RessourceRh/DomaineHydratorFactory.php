<?php

namespace Application\Form\RessourceRh;

use Application\Service\FamilleProfessionnelle\FamilleProfessionnelleService;
use Interop\Container\ContainerInterface;

class DomaineHydratorFactory {

    public function __invoke(ContainerInterface $container)
    {
        /**
         * @var FamilleProfessionnelleService $familleService
         */
        $familleService = $container->get(FamilleProfessionnelleService::class);

        /** @var DomaineHydrator $hydrator */
        $hydrator = new DomaineHydrator();
        $hydrator->setFamilleProfessionnelleService($familleService);
        return $hydrator;
    }
}