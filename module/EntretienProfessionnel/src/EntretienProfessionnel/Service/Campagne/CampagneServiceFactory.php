<?php

namespace EntretienProfessionnel\Service\Campagne;

use Application\Service\Agent\AgentService;
use Doctrine\ORM\EntityManager;
use EntretienProfessionnel\Service\AgentForceSansObligation\AgentForceSansObligationService;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Structure\Service\Structure\StructureService;
use UnicaenEtat\Service\EtatType\EtatTypeService;
use UnicaenParametre\Service\Parametre\ParametreService;

class CampagneServiceFactory
{

    /**
     * @param ContainerInterface $container
     * @return CampagneService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CampagneService
    {
        /**
         * @var EntityManager $entityManager
         * @var AgentService $agentService
         * @var AgentForceSansObligationService $agentForceService
         * @var EtatTypeService $etatTypeService
         * @var ParametreService $parametreService
         * @var StructureService $structureService
         */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $agentService = $container->get(AgentService::class);
        $agentForceService = $container->get(AgentForceSansObligationService::class);
        $etatTypeService = $container->get(EtatTypeService::class);
        $parametreService = $container->get(ParametreService::class);
        $structureService = $container->get(StructureService::class);

        $service = new CampagneService();
        $service->setObjectManager($entityManager);
        $service->setAgentService($agentService);
        $service->setAgentForceSansObligationService($agentForceService);
        $service->setEtatTypeService($etatTypeService);
        $service->setParametreService($parametreService);
        $service->setStructureService($structureService);
        return $service;
    }
}