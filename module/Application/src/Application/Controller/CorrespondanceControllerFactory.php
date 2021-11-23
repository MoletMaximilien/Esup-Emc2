<?php

namespace Application\Controller;

use Application\Service\Correspondance\CorrespondanceService;
use Interop\Container\ContainerInterface;

class CorrespondanceControllerFactory {

    public function __invoke(ContainerInterface $container) : CorrespondanceController
    {
        /**
         * @var CorrespondanceService $correspondanceService
         */
        $correspondanceService = $container->get(CorrespondanceService::class);

        $controller = new CorrespondanceController();
        $controller->setCorrespondanceService($correspondanceService);
        return $controller;
    }
}