<?php

namespace UnicaenEtat\Form\Etat;

use UnicaenEtat\Entity\Db\Etat;
use UnicaenEtat\Service\EtatType\EtatTypeServiceAwareTrait;
use Zend\Hydrator\HydratorInterface;

class EtatHydrator implements HydratorInterface {
    use EtatTypeServiceAwareTrait;

    /**
     * @param Etat $object
     * @return array
     */
    public function extract($object)
    {
        $data = [
            'code' => ($object)?$object->getCode():null,
            'libelle' => ($object)?$object->getLibelle():null,
            'icone' => ($object)?$object->getIcone():null,
            'couleur' => ($object)?$object->getCouleur():null,
            'type' => ($object AND $object->getType())?$object->getType()->getId():null,
        ];
        return $data;
    }

    /**
     * @param array $data
     * @param Etat $object
     * @return Etat
     */
    public function hydrate(array $data, $object)
    {
        $code = (isset($data['code']) AND trim($data['code']) !== "")?trim($data['code']):null;
        $libelle = (isset($data['libelle']) AND trim($data['libelle']) !== "")?trim($data['libelle']):null;
        $icone = (isset($data['icone']) AND trim($data['icone']) !== "")?trim($data['icone']):null;
        $couleur = (isset($data['couleur']) AND trim($data['couleur']) !== "")?trim($data['couleur']):null;
        $type = isset($data['couleur'])?$this->getEtatTypeService()->getEtatType($data['type']):null;

        $object->setCode($code);
        $object->setLibelle($libelle);
        $object->setIcone($icone);
        $object->setCouleur($couleur);
        $object->setType($type);

        return $object;
    }

}