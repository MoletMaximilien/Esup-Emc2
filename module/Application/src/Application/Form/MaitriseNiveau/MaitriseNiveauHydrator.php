<?php

namespace Application\Form\MaitriseNiveau;

use Application\Entity\Db\MaitriseNiveau;
use Zend\Hydrator\HydratorInterface;

class MaitriseNiveauHydrator implements HydratorInterface {

    /**
     * @param MaitriseNiveau $object
     * @return array
     */
    public function extract($object)
    {
        $data = [
            'libelle' => $object->getLibelle(),
            'niveau' => $object->getNiveau(),
            'description' => $object->getDescription(),
            'type' => $object->getType(),
        ];
        return $data;
    }

    /**
     * @param array $data
     * @param MaitriseNiveau $object
     * @return MaitriseNiveau
     */
    public function hydrate(array $data, $object)
    {
        $libelle = (isset($data['libelle']) AND trim($data['libelle']) !== '')?trim($data['libelle']):null;
        $niveau = (isset($data['niveau']))?$data['niveau']:null;
        $type = (isset($data['type']))?$data['type']:null;
        $description = (isset($data['description']) AND trim($data['description']) !== '')?trim($data['description']):null;

        $object->setLibelle($libelle);
        $object->setType($type);
        $object->setNiveau($niveau);
        $object->setDescription($description);
        return $object;
    }


}