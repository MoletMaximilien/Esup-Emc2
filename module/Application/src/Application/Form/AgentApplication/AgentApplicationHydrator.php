<?php

namespace Application\Form\AgentApplication;

use Application\Entity\Db\AgentApplication;
use Application\Service\Application\ApplicationServiceAwareTrait;
use Zend\Hydrator\HydratorInterface;

class AgentApplicationHydrator implements HydratorInterface {
    use ApplicationServiceAwareTrait;

    /**
     * @param AgentApplication $object
     * @return array
     */
    public function extract($object)
    {
        $data = [
            'application' => ($object->getApplication())?$object->getApplication()->getId():null,
            'type' => ($object->getType())?$object->getType():null,
            'annee' => ($object->getAnnee())?$object->getAnnee():null,
        ];
        return $data;
    }

    /**
     * @param array $data
     * @param AgentApplication $object
     * @return AgentApplication
     */
    public function hydrate(array $data, $object)
    {
        $application = isset($data['application'])?$this->getApplicationService()->getApplication($data['application']):null;
        $type = isset($data['type'])?$data['type']:"Auto-formation";
        $annee= (isset($data['annee']) AND $data['annee'] !== "")? ((int) $data['annee']):null;

        $object->setApplication($application);
        $object->setType($type);
        $object->setAnnee($annee);

        return $object;
    }
}