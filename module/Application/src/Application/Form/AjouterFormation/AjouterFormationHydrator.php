<?php

namespace Application\Form\AjouterFormation;

use Application\Entity\Db\Formation;
use Zend\Hydrator\HydratorInterface;

class AjouterFormationHydrator implements HydratorInterface {

    public function extract($object)
    {
        return [];
    }

    public function hydrate(array $data, $object)
    {
        return $object;
    }

}