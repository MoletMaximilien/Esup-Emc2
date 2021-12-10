<?php

namespace EntretienProfessionnel\Controller;

use Application\Form\SelectionAgent\SelectionAgentForm;
use Application\Service\Agent\AgentService;
use Application\Service\Structure\StructureService;
use EntretienProfessionnel\Service\Campagne\CampagneService;
use EntretienProfessionnel\Service\Delegue\DelegueService;
use Interop\Container\ContainerInterface;

class DelegueControllerFactory {

    /**
     * @param ContainerInterface $container
     * @return DelegueController
     */
    public function __invoke(ContainerInterface $container) : DelegueController
    {
        /**
         * @var AgentService $agentService
         * @var CampagneService $campagneService
         * @var DelegueService $delegueService
         * @var StructureService $structureService
         */
        $agentService = $container->get(AgentService::class);
        $campagneService = $container->get(CampagneService::class);
        $delegueService = $container->get(DelegueService::class);
        $structureService = $container->get(StructureService::class);

        /**
         * @var SelectionAgentForm $selectionAgentForm
         */
        $selectionAgentForm  = $container->get('FormElementManager')->get(SelectionAgentForm::class);

        $controller = new DelegueController();
        $controller->setAgentService($agentService);
        $controller->setCampagneService($campagneService);
        $controller->setDelegueService($delegueService);
        $controller->setStructureService($structureService);
        $controller->setSelectionAgentForm($selectionAgentForm);

        return $controller;
    }
}