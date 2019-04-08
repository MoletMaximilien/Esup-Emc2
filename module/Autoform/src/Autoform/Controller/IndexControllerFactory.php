<?php

namespace Autoform\Controller;

use Zend\Mvc\Controller\ControllerManager;

class IndexControllerFactory {

    public function __invoke(ControllerManager $manager)
    {
        /** @var IndexController $controller*/
        $controller = new IndexController();
        return $controller;
    }
}