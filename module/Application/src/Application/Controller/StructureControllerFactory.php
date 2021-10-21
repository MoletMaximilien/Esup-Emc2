<?php

namespace Application\Controller;

use Application\Form\AgentMissionSpecifique\AgentMissionSpecifiqueForm;
use Application\Form\AjouterGestionnaire\AjouterGestionnaireForm;
use Application\Form\HasDescription\HasDescriptionForm;
use Application\Form\HasDescription\HasDescriptionFormAwareTrait;
use Application\Form\SelectionAgent\SelectionAgentForm;
use Application\Service\Agent\AgentService;
use Application\Service\FichePoste\FichePosteService;
use Application\Service\FicheProfil\FicheProfilService;
use Application\Service\MissionSpecifique\MissionSpecifiqueAffectationService;
use Application\Service\Poste\PosteService;
use Application\Service\SpecificitePoste\SpecificitePosteService;
use Application\Service\Structure\StructureService;
use Application\Service\StructureAgentForce\StructureAgentForceService;
use EntretienProfessionnel\Service\Campagne\CampagneService;
use EntretienProfessionnel\Service\Delegue\DelegueService;
use EntretienProfessionnel\Service\EntretienProfessionnel\EntretienProfessionnelService;
use Formation\Service\FormationInstanceInscrit\FormationInstanceInscritService;
use Interop\Container\ContainerInterface;
use UnicaenUtilisateur\Service\Role\RoleService;
use UnicaenUtilisateur\Service\User\UserService;

class StructureControllerFactory {

    public function __invoke(ContainerInterface $container)
    {
        /**
         * @var AgentService $agentService
         * @var EntretienProfessionnelService $entretienService
         * @var CampagneService $campagneService
         * @var DelegueService $delegueService
         * @var FichePosteService $fichePosteService
         * @var FicheProfilService $ficheProfilService
         * @var FormationInstanceInscritService $formationInstanceInscritService
         * @var MissionSpecifiqueAffectationService $missionSpecifiqueAffectationService
         * @var PosteService $posteService
         * @var RoleService $roleService
         * @var SpecificitePosteService $specificiteService
         * @var StructureService $structureService
         * @var StructureAgentForceService $structureAgentForceService
         * @var UserService $userService
         */
        $agentService = $container->get(AgentService::class);
        $entretienService = $container->get(EntretienProfessionnelService::class);
        $campagneService = $container->get(CampagneService::class);
        $delegueService = $container->get(DelegueService::class);
        $fichePosteService = $container->get(FichePosteService::class);
        $ficheProfilService = $container->get(FicheProfilService::class);
        $formationInstanceInscritService = $container->get(FormationInstanceInscritService::class);
        $missionSpecifiqueAffectationService = $container->get(MissionSpecifiqueAffectationService::class);
        $posteService = $container->get(PosteService::class);
        $roleService = $container->get(RoleService::class);
        $specificiteService = $container->get(SpecificitePosteService::class);
        $structureService = $container->get(StructureService::class);
        $structureAgentForceService = $container->get(StructureAgentForceService::class);
        $userService = $container->get(UserService::class);

        /**
         * @var AgentMissionSpecifiqueForm $affectationForm
         * @var AjouterGestionnaireForm $ajouterGestionnaire
         * @var SelectionAgentForm $selectionAgentForm
         * @var HasDescriptionFormAwareTrait $hasDescriptionForm
         */
        $affectationForm = $container->get('FormElementManager')->get(AgentMissionSpecifiqueForm::class);
        $ajouterGestionnaire = $container->get('FormElementManager')->get(AjouterGestionnaireForm::class);
        $selectionAgentForm = $container->get('FormElementManager')->get(SelectionAgentForm::class);
        $hasDescriptionForm = $container->get('FormElementManager')->get(HasDescriptionForm::class);

        /** @var StructureController $controller */
        $controller = new StructureController();

        $controller->setAgentService($agentService);
        $controller->setEntretienProfessionnelService($entretienService);
        $controller->setCampagneService($campagneService);
        $controller->setDelegueService($delegueService);
        $controller->setFichePosteService($fichePosteService);
        $controller->setFicheProfilService($ficheProfilService);
        $controller->setFormationInstanceInscritService($formationInstanceInscritService);
        $controller->setMissionSpecifiqueAffectationService($missionSpecifiqueAffectationService);
        $controller->setPosteService($posteService);
        $controller->setRoleService($roleService);
        $controller->setSpecificitePosteService($specificiteService);
        $controller->setStructureService($structureService);
        $controller->setStructureAgentForceService($structureAgentForceService);
        $controller->setUserService($userService);

        $controller->setAgentMissionSpecifiqueForm($affectationForm);
        $controller->setAjouterGestionnaireForm($ajouterGestionnaire);
        $controller->setSelectionAgentForm($selectionAgentForm);
        $controller->setHasDescriptionForm($hasDescriptionForm);

        return $controller;
    }
}