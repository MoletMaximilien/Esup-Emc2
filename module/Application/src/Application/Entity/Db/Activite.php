<?php

namespace Application\Entity\Db;

use Application\Entity\Db\Interfaces\HasApplicationCollectionInterface;
use Application\Entity\Db\Interfaces\HasCompetenceCollectionInterface;
use Application\Entity\Db\Traits\HasApplicationCollectionTrait;
use Application\Entity\Db\Traits\HasCompetenceCollectionTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Formation\Entity\Db\Formation;
use UnicaenUtilisateur\Entity\HistoriqueAwareInterface;
use UnicaenUtilisateur\Entity\HistoriqueAwareTrait;

class Activite implements HistoriqueAwareInterface,
    HasApplicationCollectionInterface, HasCompetenceCollectionInterface
{
    use HistoriqueAwareTrait;
    use HasApplicationCollectionTrait;
    use HasCompetenceCollectionTrait;

    /** @var int */
    private $id;
    /** @var ArrayCollection */
    private $libelles;
    /** @var ArrayCollection */
    private $descriptions;
    /** @var ArrayCollection */
    private $formations;
    /** @var ArrayCollection (FicheMetier) */
    private $fiches;

    public function __construct()
    {
        $this->libelles = new ArrayCollection();
        $this->descriptions = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->competences = new ArrayCollection();
        $this->formations = new ArrayCollection();
        $this->fiches = new ArrayCollection();
    }
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /** LIBELLE *******************************************************************************************************/

    /**
     * @return ActiviteLibelle
     */
    public function getCurrentActiviteLibelle()
    {
        $current = null;
        /** @var ActiviteLibelle $activiteLibelle */
        foreach ($this->libelles as $activiteLibelle) {
            if ($activiteLibelle->estNonHistorise()) return $activiteLibelle;
        }
        return null;
    }

    /**
     * @return string
     */
    public function getLibelle()
    {
        $libelle = null;
        /** @var ActiviteLibelle $instance */
        foreach ($this->libelles as $instance) {
            if ($instance->getHistoDestruction() === null) {
                if ($libelle === null) return $instance->getLibelle();
                else return "<span class='probleme'><strong>PLUSIEURS LIBELLÉS ACTIFS !</strong></span>";
            }
        }
        return  "<span class='probleme'><strong>AUCUN LIBELLÉ !</strong></span>";
    }

    /** DESCRIPTION ***************************************************************************************************/

    /**
     * @return string
     */
    public function getDescription()
    {
        $desctiptions = $this->getDescriptions();
        $desctiption = '';
        $desctiption .= '<ul>';
        foreach ($desctiptions as $item) $desctiption .= '<li>' . $item->getLibelle() .'</li>';
        $desctiption .= '</ul>';
        return $desctiption;
    }

    /**
     * @return ActiviteDescription[]
     */
    public function getDescriptions()
    {
        $descriptions = [];
        /** @var ActiviteDescription $activiteDescription */
        foreach ($this->descriptions as $activiteDescription) {
            if ($activiteDescription->estNonHistorise()) $descriptions[] = $activiteDescription;
        }
        return $descriptions;
    }

    /**
     * @return Activite
     */
    public function clearDescriptions()
    {
        $this->descriptions->clear();
        return $this;
    }

    /** APPLICATIONS - VOIR HasApplicationCollectionTrait *************************************************************/
    /** COMPETENCES - VOIR HasCompetenceCollectionTrait ***************************************************************/

    /**
     * @return Competence[]
     */
    public function getCompetences()
    {
        $competences = [];
        /** @var ActiviteCompetence $activiteCompetence */
        foreach ($this->competences as $activiteCompetence) {
            if ($activiteCompetence->estNonHistorise()) $competences[] = $activiteCompetence->getCompetence();
        }
        return $competences;
    }

    /** FORMATIONS ****************************************************************************************************/

    /**
     * @return ArrayCollection (ActiviteFormation)
     */
    public function getFormationsCollection()
    {
        return $this->formations;
    }

    /**
     * @return Formation[]
     */
    public function getFormations()
    {
        $formations = [];
        /** @var ActiviteFormation $activiteFormation */
        foreach ($this->formations as $activiteFormation) {
            if ($activiteFormation->estNonHistorise()) $formations[] = $activiteFormation->getFormation();
        }
        return $formations;
    }

    /**
     * @param Formation $formation
     * @return boolean
     */
    public function hasFormation(Formation $formation)
    {
        /** @var ActiviteFormation $activiteFormation */
        foreach ($this->formations as $activiteFormation) {
            if ($activiteFormation->estNonHistorise() AND $activiteFormation->getFormation() === $formation) return true;
        }
        return false;
    }

    /** FICHE METIER **************************************************************************************************/

    /**
     * @return ArrayCollection (FicheMetier)
     */
    public function getFichesMetiers()
    {
        return $this->fiches;
    }

}