<?php

namespace Application\Entity\Db;

use Application\Entity\HasAgentInterface;
use UnicaenApp\Entity\HistoriqueAwareInterface;
use UnicaenApp\Entity\HistoriqueAwareTrait;

class StructureAgentForce implements HistoriqueAwareInterface, HasAgentInterface {
    use HistoriqueAwareTrait;

    /** @var integer */
    private $id;
    /** @var Structure */
    private $structure;
    /** @var Agent */
    private $agent;

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return Structure|null
     */
    public function getStructure() : ?Structure
    {
        return $this->structure;
    }

    /**
     * @param Structure|null $structure
     * @return $this
     */
    public function setStructure(?Structure $structure) : StructureAgentForce
    {
        $this->structure = $structure;
        return $this;
    }

    /**
     * @return Agent|null
     */
    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    /**
     * @param Agent|null $agent
     * @return StructureAgentForce
     */
    public function setAgent(?Agent $agent): StructureAgentForce
    {
        $this->agent = $agent;
        return $this;
    }

}