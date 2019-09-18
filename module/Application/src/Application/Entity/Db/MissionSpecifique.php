<?php

namespace Application\Entity\Db;

use Doctrine\Common\Collections\ArrayCollection;
use UnicaenApp\Entity\HistoriqueAwareTrait;

class MissionSpecifique {
    use HistoriqueAwareTrait;

    /** @var integer */
    private $id;
    /** @var string */
    private $libelle;
    /** @var MissionSpecifiqueTheme */
    private $theme;
    /** @var MissionSpecifiqueType */
    private $type;
    /** @var ArrayCollection */
    private $affectations;

    public function __construct()
    {
        $this->affectations = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return MissionSpecifique
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param string $libelle
     * @return MissionSpecifique
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
        return $this;
    }

    /**
     * @return MissionSpecifiqueTheme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param MissionSpecifiqueTheme $theme
     * @return MissionSpecifique
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * @return MissionSpecifiqueType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param MissionSpecifiqueType $type
     * @return MissionSpecifique
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return AgentMissionSpecifique[]
     */
    public function getAffectations()
    {
        return $this->affectations->toArray();
    }

}