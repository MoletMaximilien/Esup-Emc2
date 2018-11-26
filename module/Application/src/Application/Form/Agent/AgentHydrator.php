<?php

namespace Application\Form\Agent;

use Application\Entity\Db\Agent;
use Application\Service\RessourceRh\RessourceRhServiceAwareTrait;
use DateTime;
use Zend\Stdlib\Hydrator\HydratorInterface;

class AgentHydrator implements HydratorInterface {
    use RessourceRhServiceAwareTrait;

    /**
     * @param Agent $object
     * @return array
     */
    public function extract($object)
    {
        $data = [
            'nom'               => $object->getNom(),
            'prenom'            => $object->getPrenom(),
            'dateDebut'         => ($object->getDateDebut())?$object->getDateDebut()->format("d/m/Y"):"",
            'dateFin'           => ($object->getDateFin())?$object->getDateFin()->format("d/m/Y"):"",
            'quotite'           => $object->getQuotite(),
            'status'            => $object->getStatus(),
            'correspondance'    => $object->getCorrespondance(),
        ];

        return $data;
    }

    /**
     * @param array $data
     * @param Agent $object
     * @return Agent
     */
    public function hydrate(array $data, $object)
    {
        $status = $this->getRessourceRhService()->getAgentStatus($data['status']);
        $correspondance = $this->getRessourceRhService()->getCorrespondance($data['correspondance']);

        $object->setPrenom($data['prenom']);
        $object->setNom($data['nom']);
        $object->setQuotite($data['quotite']);
        if ($data['dateDebut']) {
            $date = DateTime::createFromFormat('d/m/Y', $data['dateDebut']);
            $object->setDateDebut($date);
        }
        if ($data['dateFin']) {
            $date = DateTime::createFromFormat('d/m/Y', $data['dateFin']);
            $object->setDateFin($date);
        }
        $object->setStatus($status);
        $object->setCorrespondance($correspondance);

        return $object;
    }

}