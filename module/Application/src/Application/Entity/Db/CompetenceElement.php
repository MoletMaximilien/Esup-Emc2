<?php

namespace Application\Entity\Db;

use UnicaenUtilisateur\Entity\HistoriqueAwareInterface;
use UnicaenUtilisateur\Entity\HistoriqueAwareTrait;
use UnicaenValidation\Entity\ValidableAwareTrait;
use UnicaenValidation\Entity\ValidableInterface;

class CompetenceElement implements HistoriqueAwareInterface, ValidableInterface {
    use HistoriqueAwareTrait;
    use ValidableAwareTrait;

    /** @var integer */
    private $id;
    /** @var Competence */
    private $competence;
    /** @var string */
    private $commentaire;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Competence|null
     */
    public function getCompetence(): ?Competence
    {
        return $this->competence;
    }

    /**
     * @param Competence|null $competence
     * @return CompetenceElement
     */
    public function setCompetence(?Competence $competence): CompetenceElement
    {
        $this->competence = $competence;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    /**
     * @param string|null $commentaire
     * @return CompetenceElement
     */
    public function setCommentaire(?string $commentaire): CompetenceElement
    {
        $this->commentaire = $commentaire;
        return $this;
    }

}