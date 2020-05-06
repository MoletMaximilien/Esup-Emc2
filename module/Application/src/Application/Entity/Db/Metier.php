<?php

namespace Application\Entity\Db;

use Doctrine\Common\Collections\ArrayCollection;
use UnicaenUtilisateur\Entity\HistoriqueAwareInterface;
use UnicaenUtilisateur\Entity\HistoriqueAwareTrait;

class Metier implements HistoriqueAwareInterface {
    use HistoriqueAwareTrait;

    const Prefix_REFERENS = "https://data.enseignementsup-recherche.gouv.fr/pages/fiche_emploi_type_referens_iii_itrf/?refine.referens_id=";

    /** @var integer */
    private $id;
    /** @var string */
    private $libelle;

    /** @var Domaine */
    private $domaine;
    /** @var string */
    private $fonction;
    /** @var string */
    private $emploiType;
    /** @var string */
    private $lien;


    /** @var ArrayCollection (MetierReference) */
    private $references;
    /** @var ArrayCollection (FicheMetierType) */
    private $fichesMetiers;

    public function __construct()
    {
        $this->fichesMetiers = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    /**
     * @return string
     */
    public function getFonction()
    {
        return $this->fonction;
    }

    /**
     * @param string $fonction
     * @return Metier
     */
    public function setFonction($fonction)
    {
        $this->fonction = $fonction;
        return $this;
    }

    /**
     * @return Domaine
     */
    public function getDomaine()
    {
        return $this->domaine;
    }

    /**
     * @param Domaine $domaine
     * @return Metier
     */
    public function setDomaine($domaine)
    {
        $this->domaine = $domaine;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmploiType()
    {
        return $this->emploiType;
    }

    /**
     * @param string $emploiType
     * @return Metier
     */
    public function setEmploiType(string $emploiType)
    {
        $this->emploiType = $emploiType;
        return $this;
    }

    /**
     * @return string
     */
    public function getLien()
    {
        return $this->lien;
    }

    /**
     * @param string $lien
     * @return Metier
     */
    public function setLien($lien)
    {
        $this->lien = $lien;
        return $this;
    }

    public function __toString()
    {
        return $this->getLibelle();
    }

    /**
     * @return ArrayCollection
     */
    public function getFichesMetiers()
    {
        return $this->fichesMetiers;
    }

    /**
     * @return MetierReference[]
     */
    public function getReferences()
    {
        return $this->references->toArray();
    }

}