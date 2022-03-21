<?php

namespace Application\Controller;

use Application\Service\Agent\AgentService;
use Application\Service\FicheMetier\FicheMetierService;
use Element\Form\ApplicationElement\ApplicationElementForm;
use Element\Form\CompetenceElement\CompetenceElementForm;
use Element\Form\SelectionNiveau\SelectionNiveauForm;
use Element\Service\Application\ApplicationService;
use Element\Service\ApplicationElement\ApplicationElementService;
use Element\Service\Competence\CompetenceService;
use Element\Service\CompetenceElement\CompetenceElementService;
use Element\Service\Niveau\NiveauService;
use Formation\Service\FormationElement\FormationElementService;
use Interop\Container\ContainerInterface;

class ElementControllerFactory {

    /**
     * @param ContainerInterface $container
     * @return ElementController
     */
    public function __invoke(ContainerInterface $container) : ElementController
    {
        /**
         * @var AgentService $agentService
         * @var ApplicationService $applicationService
         * @var ApplicationElementService $applicationElementService
         * @var CompetenceService $competenceService
         * @var CompetenceElementService $competenceElementService
         * @var FicheMetierService $ficheMetierService
         * @var FormationElementService $formationElementService
         * @var NiveauService $niveauService
         */
        $agentService = $container->get(AgentService::class);
        $applicationService = $container->get(ApplicationService::class);
        $applicationElementService = $container->get(ApplicationElementService::class);
        $competenceService = $container->get(CompetenceService::class);
        $competenceElementService = $container->get(CompetenceElementService::class);
        $ficheMetierService = $container->get(FicheMetierService::class);
        $formationElementService = $container->get(FormationElementService::class);
        $niveauService = $container->get(NiveauService::class);

        /**
         * @var ApplicationElementForm $applicationElementForm
         * @var CompetenceElementForm $competenceElementForm
         * @var SelectionNiveauForm $selectionMaitriseForm
         */
        $applicationElementForm = $container->get('FormElementManager')->get(ApplicationElementForm::class);
        $competenceElementForm = $container->get('FormElementManager')->get(CompetenceElementForm::class);
        $selectionMaitriseForm = $container->get('FormElementManager')->get(SelectionNiveauForm::class);

        $controller = new ElementController();
        $controller->setAgentService($agentService);
        $controller->setApplicationService($applicationService);
        $controller->setApplicationElementService($applicationElementService);
        $controller->setCompetenceService($competenceService);
        $controller->setCompetenceElementService($competenceElementService);
        $controller->setFicheMetierService($ficheMetierService);
        $controller->setFormationElementService($formationElementService);
        $controller->setNiveauService($niveauService);
        $controller->setApplicationElementForm($applicationElementForm);
        $controller->setCompetenceElementForm($competenceElementForm);
        $controller->setSelectionNiveauForm($selectionMaitriseForm);
        return $controller;
    }
}