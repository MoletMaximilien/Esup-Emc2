<?php

namespace Application\Entity\Db;

use Application\Entity\Db\Interfaces\HasPeriodeInterface;
use Application\Entity\Db\Traits\DbImportableAwareTrait;
use Application\Entity\Db\Traits\HasPeriodeTrait;
use Carriere\Entity\Db\Corps;
use Carriere\Entity\Db\Correspondance;
use Carriere\Entity\Db\EmploiType;
use Carriere\Entity\Db\Grade;
use Structure\Entity\Db\Structure;

/**
 * Données synchronisées depuis Octopus :
 * - pas de setter sur les données ainsi remontées
 */
class AgentGrade implements HasPeriodeInterface {
    use DbImportableAwareTrait;
    use HasPeriodeTrait;

    private ?string $id = null;
    private ?Agent $agent = null;
    private ?Structure $structure = null;
    private ?Corps $corps = null;
    private ?Grade $grade = null;
    private ?Correspondance $correspondance = null;
    private ?EmploiType $emploiType = null;

    public function getId() : string
    {
        return $this->id;
    }

    public function getAgent() : Agent
    {
        return $this->agent;
    }

    public function getStructure() : ?Structure
    {
        return $this->structure;
    }

    public function getCorps() : ?Corps
    {
        return $this->corps;
    }

    public function getGrade() : ?Grade
    {
        return $this->grade;
    }

    public function getCorrespondance() : ?Correspondance
    {
        return $this->correspondance;
    }

    public function getEmploiType() : ?EmploiType
    {
        return $this->emploiType;
    }
}