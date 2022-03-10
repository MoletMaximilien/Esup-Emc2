<?php

namespace Carriere\Controller;

use Application\Form\ModifierNiveau\ModifierNiveauForm;
use Application\Service\Niveau\NiveauService;
use Carriere\Service\Categorie\CategorieService;
use Carriere\Service\Corps\CorpsService;
use Interop\Container\ContainerInterface;

class CorpsControllerFactory {

    /**
     * @param ContainerInterface $container
     * @return CorpsController
     */
    public function __invoke(ContainerInterface $container) : CorpsController
    {
        /**
         * @var CategorieService $categorieService
         * @var CorpsService $corpsService
         * @var NiveauService $niveauService
         */
        $categorieService = $container->get(CategorieService::class);
        $corpsService = $container->get(CorpsService::class);
        $niveauService = $container->get(NiveauService::class);

        /**
         * @var ModifierNiveauForm $modifierNiveauForm
         */
        $modifierNiveauForm = $container->get('FormElementManager')->get(ModifierNiveauForm::class);

        /** @var CorpsController $controller */
        $controller = new CorpsController();
        $controller->setCategorieService($categorieService);
        $controller->setCorpsService($corpsService);
        $controller->setNiveauService($niveauService);
        $controller->setModifierNiveauForm($modifierNiveauForm);
        return $controller;
    }
}