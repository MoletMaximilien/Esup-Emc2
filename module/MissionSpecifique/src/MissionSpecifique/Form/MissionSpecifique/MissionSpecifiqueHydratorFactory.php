<?php

namespace MissionSpecifique\Form\MissionSpecifique;

use MissionSpecifique\Service\MissionSpecifique\MissionSpecifiqueService;
use MissionSpecifique\Service\MissionSpecifiqueTheme\MissionSpecifiqueThemeService;
use MissionSpecifique\Service\MissionSpecifiqueType\MissionSpecifiqueTypeService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class MissionSpecifiqueHydratorFactory {

    /**
     * @param ContainerInterface $container
     * @return MissionSpecifiqueHydrator
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container) : MissionSpecifiqueHydrator
    {
        /**
         * @var MissionSpecifiqueService $missionSpecifiqueService
         * @var MissionSpecifiqueThemeService $missionSpecifiqueThemeService
         * @var MissionSpecifiqueTypeService $missionSpecifiqueTypeService
         */
        $missionSpecifiqueService = $container->get(MissionSpecifiqueService::class);
        $missionSpecifiqueThemeService = $container->get(MissionSpecifiqueThemeService::class);
        $missionSpecifiqueTypeService = $container->get(MissionSpecifiqueTypeService::class);

        $hydrator = new MissionSpecifiqueHydrator();
        $hydrator->setMissionSpecifiqueService($missionSpecifiqueService);
        $hydrator->setMissionSpecifiqueThemeService($missionSpecifiqueThemeService);
        $hydrator->setMissionSpecifiqueTypeService($missionSpecifiqueTypeService);
        return $hydrator;
    }
}