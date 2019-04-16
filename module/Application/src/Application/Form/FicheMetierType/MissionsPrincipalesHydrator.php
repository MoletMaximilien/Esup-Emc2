<?php

namespace Application\Form\FicheMetierType;

use Application\Entity\Db\FicheMetierType;
use Zend\Stdlib\Hydrator\HydratorInterface;

class MissionsPrincipalesHydrator implements HydratorInterface {

    /**
     * @param FicheMetierType $object
     * @return array
     */
    public function extract($object)
    {
        $data = [
            'description' => $object->getMissionsPrincipales(),
        ];
        return $data;
    }

    /**
     * @param array $data
     * @param FicheMetierType $object
     * @return FicheMetierType
     */
    public function hydrate(array $data, $object)
    {
        $object->setMissionsPrincipales($data['description']);
        return $object;
    }

}