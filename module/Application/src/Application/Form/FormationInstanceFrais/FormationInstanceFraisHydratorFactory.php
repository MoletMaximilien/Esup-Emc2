<?php

namespace Application\Form\FormationInstanceFrais;

use Interop\Container\ContainerInterface;

class FormationInstanceFraisHydratorFactory {

    /**
     * @param ContainerInterface $container
     * @return FormationInstanceFraisHydrator
     */
    public function __invoke(ContainerInterface $container)
    {
        $hydrator = new FormationInstanceFraisHydrator();
        return $hydrator;
    }
}