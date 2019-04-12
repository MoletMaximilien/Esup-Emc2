<?php

namespace Application\Controller\FichePoste;

use Application\Form\AssocierAgent\AssocierAgentForm;
use Application\Form\SpecificitePoste\SpecificitePosteForm;
use Application\Service\Agent\AgentService;
use Application\Service\FichePoste\FichePosteService;
use Zend\Mvc\Controller\ControllerManager;

class FichePosteControllerFactory {

    public function __invoke(ControllerManager $manager)
    {
        /**
         * @var AgentService $agentService
         * @var FichePosteService $fichePosteService
         */
        $agentService = $manager->getServiceLocator()->get(AgentService::class);
        $fichePosteService = $manager->getServiceLocator()->get(FichePosteService::class);

        /**
         * @var AssocierAgentForm $associerAgentForm
         * @var SpecificitePosteForm $specificiftePosteForm
         */
        $associerAgentForm = $manager->getServiceLocator()->get('FormElementManager')->get(AssocierAgentForm::class);
        $specificiftePosteForm = $manager->getServiceLocator()->get('FormElementManager')->get(SpecificitePosteForm::class);

        /** @var FichePosteController $controller */
        $controller = new FichePosteController();

        $controller->setAgentService($agentService);
        $controller->setFichePosteService($fichePosteService);

        $controller->setAssocierAgentForm($associerAgentForm);
        $controller->setSpecificitePosteForm($specificiftePosteForm);
        return $controller;
    }
}