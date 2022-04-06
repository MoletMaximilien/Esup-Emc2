<?php

namespace Element\Controller;

use Element\Service\Competence\CompetenceService;
use Element\Service\CompetenceTheme\CompetenceThemeService;
use Element\Service\CompetenceType\CompetenceTypeService;
use Interop\Container\ContainerInterface;

class CompetenceImporterControllerFactory {

    public function __invoke(ContainerInterface $container) : CompetenceImporterController
    {
        /**
         * @var CompetenceService $competenceService
         * @var CompetenceThemeService $competenceThemeService
         * @var CompetenceTypeService $competenceTypeService
         */
        $competenceService = $container->get(CompetenceService::class);
        $competenceThemeService = $container->get(CompetenceThemeService::class);
        $competenceTypeService = $container->get(CompetenceTypeService::class);

        $controller = new CompetenceImporterController();
        $controller->setCompetenceService($competenceService);
        $controller->setCompetenceThemeService($competenceThemeService);
        $controller->setCompetenceTypeService($competenceTypeService);
        return $controller;
    }
}