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

    /** @var ArrayCollection (Domaine) */
    private  $domaines;
    /** @var string */
    private $fonction;
    /** @var boolean */
    private $hasExpertise;


    /** @var ArrayCollection (MetierReference) */
    private $references;
    /** @var ArrayCollection (FicheMetierType) */
    private $fichesMetiers;

    public function __construct()
    {
        $this->references = new ArrayCollection();
        $this->fichesMetiers = new ArrayCollection();
        $this->domaines = new ArrayCollection();
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
     * @return bool
     */
   public function hasExpertise() {
        return $this->hasExpertise;
   }

    /**
     * @param bool $has
     * @return $this
     */
   public function setExpertise(bool $has) {
       $this->hasExpertise = $has;
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

    /**
     * @return Domaine[]
     */
    public function getDomaines()
    {
        $domaines =  $this->domaines->toArray();
        usort($domaines, function (Domaine $a, Domaine $b) { return $a->getLibelle() > $b->getLibelle();});
        return $domaines;
    }

    public function clearDomaines()
    {
        $this->domaines->clear();
    }

    public function addDomaine(Domaine $domaine)
    {
        $this->domaines->add($domaine);
    }

}