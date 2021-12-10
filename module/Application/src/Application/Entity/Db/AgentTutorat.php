<?php

namespace Application\Entity\Db;

use Application\Entity\Db\Interfaces\HasPeriodeInterface;
use Application\Entity\Db\Traits\HasPeriodeTrait;
use Metier\Entity\Db\Metier;
use UnicaenEtat\Entity\Db\Etat;
use UnicaenUtilisateur\Entity\HistoriqueAwareInterface;
use UnicaenUtilisateur\Entity\HistoriqueAwareTrait;

class AgentTutorat implements HistoriqueAwareInterface, HasPeriodeInterface {
    use HasPeriodeTrait;
    use HistoriqueAwareTrait;

    /** @var int */
    private $id;
    /** @var Agent */
    private $agent;
    /** @var Agent|null  */
    private $cible;
    /** @var Metier|null */
    private $metier;
    /** @var string|null */
    private $complement;
    /** @var bool|null */
    private $formation;
    /** @var Etat|null */
    private $etat;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Agent
     */
    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    /**
     * @param Agent $agent
     * @return AgentTutorat
     */
    public function setAgent(Agent $agent): AgentTutorat
    {
        $this->agent = $agent;
        return $this;
    }

    /**
     * @return Agent|null
     */
    public function getCible(): ?Agent
    {
        return $this->cible;
    }

    /**
     * @param Agent|null $cible
     * @return AgentTutorat
     */
    public function setCible(?Agent $cible): AgentTutorat
    {
        $this->cible = $cible;
        return $this;
    }

    /**
     * @return Metier|null
     */
    public function getMetier(): ?Metier
    {
        return $this->metier;
    }

    /**
     * @param Metier|null $metier
     * @return AgentTutorat
     */
    public function setMetier(?Metier $metier): AgentTutorat
    {
        $this->metier = $metier;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComplement(): ?string
    {
        return $this->complement;
    }

    /**
     * @param string|null $complement
     * @return AgentTutorat
     */
    public function setComplement(?string $complement): AgentTutorat
    {
        $this->complement = $complement;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getFormation(): ?bool
    {
        return $this->formation;
    }

    /**
     * @param bool|null $formation
     * @return AgentTutorat
     */
    public function setFormation(?bool $formation): AgentTutorat
    {
        $this->formation = $formation;
        return $this;
    }

    /**
     * @return Etat|null
     */
    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    /**
     * @param Etat|null $etat
     * @return AgentTutorat
     */
    public function setEtat(?Etat $etat): AgentTutorat
    {
        $this->etat = $etat;
        return $this;
    }
}