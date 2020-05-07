<?php

namespace Application\Form\Metier;

use Application\Entity\Db\Metier;
use Application\Service\Domaine\DomaineServiceAwareTrait;
use Zend\Hydrator\HydratorInterface;

class MetierHydrator implements HydratorInterface {
    use DomaineServiceAwareTrait;

    /**
     * @param Metier $object
     * @return array
     */
    public function extract($object)
    {
        $data = [
            'domaine' => ($object->getDomaine())?$object->getDomaine()->getId():null,
            'fonction' => $object->getFonction(),
            'libelle' => $object->getLibelle(),
            'emploi-type' => $object->getEmploiType(),
            'lien' => $object->getLien(),
        ];
        return $data;
    }

    /**
     * @param array $data
     * @param Metier $object
     * @return Metier
     */
    public function hydrate(array $data, $object)
    {
        $domaine = $this->getDomaineService()->getDomaine($data['domaine']);

        $object->setLibelle($data['libelle']);
        $object->setFonction($data['fonction']);
        $object->setDomaine($domaine);
        $object->setEmploiType($data['emploi-type']);
        $object->setLien($data['lien']);
        return $object;
    }

}