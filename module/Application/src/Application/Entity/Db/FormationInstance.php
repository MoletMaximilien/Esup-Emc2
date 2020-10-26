<?php

namespace Application\Entity\Db;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Formation\Entity\Db\FormationInstanceFormateur;
use Formation\Entity\Db\FormationInstanceJournee;
use UnicaenEtat\Entity\Db\Etat;
use UnicaenUtilisateur\Entity\HistoriqueAwareInterface;
use UnicaenUtilisateur\Entity\HistoriqueAwareTrait;

class FormationInstance implements HistoriqueAwareInterface {
    use HistoriqueAwareTrait;

    const TYPE_INTERNE      = "formation interne";
    const TYPE_EXTERNE      = "formation externe";
    const TYPE_REGIONALE    = "formation régionale";

    /** @var integer */
    private $id;
    /** @var Formation */
    private $formation;
    /** @var string */
    private $complement;

    /** @var integer */
    private $nbPlacePrincipale;
    /** @var integer */
    private $nbPlaceComplementaire;
    /** @var string */
    private $lieu;
    /** @var string */
    private $type;
    /** @var Etat */
    private $etat;

    /** @var ArrayCollection (FormationInstanceJournee) */
    private $journees;
    /** @var ArrayCollection (FormationInstanceInscrit) */
    private $inscrits;
    /** @var ArrayCollection (FormationInstanceFormateur) */
    private $formateurs;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Formation
     */
    public function getFormation()
    {
        return $this->formation;
    }

    /**
     * @param Formation|null $formation
     * @return FormationInstance
     */
    public function setFormation(?Formation $formation)
    {
        $this->formation = $formation;
        return $this;
    }

    /**
     * @return string
     */
    public function getComplement()
    {
        return $this->complement;
    }

    /**
     * @param string|null $complement
     * @return FormationInstance
     */
    public function setComplement(?string $complement)
    {
        $this->complement = $complement;
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
     * @param Etat $etat
     * @return FormationInstance
     */
    public function setEtat(Etat $etat): FormationInstance
    {
        $this->etat = $etat;
        return $this;
    }

    /** PLACE SUR LISTE  **********************************************************************************************/

    /**
     * @return int
     */
    public function getNbPlacePrincipale()
    {
        return $this->nbPlacePrincipale;
    }

    /**
     * @param int $nbPlacePrincipale
     * @return FormationInstance
     */
    public function setNbPlacePrincipale(int $nbPlacePrincipale)
    {
        $this->nbPlacePrincipale = $nbPlacePrincipale;
        return $this;
    }

    /**
     * @return int
     */
    public function getNbPlaceComplementaire()
    {
        return $this->nbPlaceComplementaire;
    }

    /**
     * @param int $nbPlaceComplementaire
     * @return FormationInstance
     */
    public function setNbPlaceComplementaire(int $nbPlaceComplementaire)
    {
        $this->nbPlaceComplementaire = $nbPlaceComplementaire;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    /**
     * @param string $lieu
     * @return FormationInstance
     */
    public function setLieu(string $lieu): FormationInstance
    {
        $this->lieu = $lieu;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return FormationInstance
     */
    public function setType(?string $type): FormationInstance
    {
        $this->type = $type;
        return $this;
    }


    /** FORMATEURS ****************************************************************************************************/

    /**
     * @return FormationInstanceFormateur[]|null
     */
    public function getFormateurs() : ?array
    {
        if ($this->formateurs === null) return null;
        return $this->formateurs->toArray();
    }

    /**
     * @return float
     */
    public function getVolumeHoraireSomme()
    {
        $somme = 0;
        /** @var FormationInstanceFormateur $formateur */
        foreach ($this->formateurs as $formateur) {
            if ($formateur->estNonHistorise() AND $formateur->getVolume()) $somme += $formateur->getVolume();
        }
        return $somme;
    }

    /** JOURNEE *******************************************************************************************************/

    public function getJournees()
    {
        if ($this->journees === null) return null;
        return $this->journees->toArray();
    }

    public function getDebut()
    {
        $minimum = null;
        foreach ($this->journees as $journee) {
            if ($journee->estNonHistorise()) {
                $split = explode("/", $journee->getJour());
                $reversed = $split[2] . "/" . $split[1] . "/" . $split[0];
                if ($minimum === null or $reversed < $minimum) $minimum = $reversed;
            }
        }
        if ($minimum !== null) {
            $split = explode("/",$minimum);
            $minimum = $split[2]."/".$split[1]."/".$split[0];
        }
        return $minimum;
    }

    public function getFin()
    {
        $maximum = null;
        /** @var FormationInstanceJournee $journee */
        foreach ($this->journees as $journee) {
            if ($journee->estNonHistorise()) {
                $split = explode("/",$journee->getJour());
                $reversed = $split[2]."/".$split[1]."/".$split[0];
                if ($maximum === null OR  $reversed > $maximum) $maximum = $reversed;
            }
        }
        if ($maximum !== null) {
            $split = explode("/",$maximum);
            $maximum = $split[2]."/".$split[1]."/".$split[0];
        }
        return $maximum;
    }

    /** INSCRIT *******************************************************************************************************/

    /**
     * @return FormationInstanceInscrit[]
     */
    public function getInscrits()
    {
        if ($this->inscrits === null) return null;
        return $this->inscrits->toArray();
    }

    /**
     * @return FormationInstanceInscrit[]
     */
    public function getListePrincipale()
    {
        if ($this->inscrits === null) return null;
        $array = array_filter($this->inscrits->toArray(), function (FormationInstanceInscrit $a) { return $a->getListe() === FormationInstanceInscrit::PRINCIPALE;});
        usort($array, function (FormationInstanceInscrit $a, FormationInstanceInscrit $b) { return $a->getAgent()->getDenomination() > $b->getAgent()->getDenomination();});
        return $array;
    }

    /**
     * @return FormationInstanceInscrit[]
     */
    public function getListeComplementaire()
    {
        if ($this->inscrits === null) return null;
        $array =  array_filter($this->inscrits->toArray(), function (FormationInstanceInscrit $a) { return $a->getListe() === FormationInstanceInscrit::COMPLEMENTAIRE;});
        usort($array, function (FormationInstanceInscrit $a, FormationInstanceInscrit $b) { return $a->getHistoCreation() > $b->getHistoCreation();});
        return $array;
    }

    /**
     * @return FormationInstanceInscrit[]
     */
    public function getListeHistorisee()
    {
        if ($this->inscrits === null) return null;
        $array=  array_filter($this->inscrits->toArray(), function (FormationInstanceInscrit $a) { return $a->estHistorise();});
        usort($array, function (FormationInstanceInscrit $a, FormationInstanceInscrit $b) { return $a->getAgent()->getDenomination() > $b->getAgent()->getDenomination();});
        return $array;
    }

    /**
     * @return bool
     */
    public function isListePrincipaleComplete()
    {
        if ($this->inscrits === null) return false;
        $array =  array_filter($this->inscrits->toArray(), function (FormationInstanceInscrit $a) { return $a->getListe() === FormationInstanceInscrit::PRINCIPALE;});
        return (count($array) >= $this->getNbPlacePrincipale());
    }

    /**
     * @return bool
     */
    public function isListeComplementaireComplete()
    {
        if ($this->inscrits === null) return false;
        $array =  array_filter($this->inscrits->toArray(), function (FormationInstanceInscrit $a) { return $a->getListe() === FormationInstanceInscrit::COMPLEMENTAIRE;});
        return (count($array) >= $this->getNbPlaceComplementaire());
    }

    public function getListeDisponible()
    {
        $liste = null;
        if ($liste === null AND ! $this->isListePrincipaleComplete()) $liste = FormationInstanceInscrit::PRINCIPALE;
        if ($liste === null AND ! $this->isListeComplementaireComplete()) $liste = FormationInstanceInscrit::COMPLEMENTAIRE;
        return $liste;
    }

    /**
     * @param Agent $agent
     * @return bool
     */
    public function hasAgent(Agent $agent)
    {
        foreach ($this->inscrits as $inscrit) {
            if ($inscrit->getAgent() === $agent) return true;
        }
        return false;
    }

    /** Fonctions pour les macros **********************************************************************************/

    public function getInstanceCode()
    {
        return $this->getFormation()->getId()."/".$this->getId();
    }

    public function getListeFormateurs()
    {
        /** @var FormationInstanceFormateur[] $formateurs */
        $formateurs = $this->getFormateurs();
        usort($formateurs, function (FormationInstanceFormateur $a, FormationInstanceFormateur $b){ return ($a->getNom()." ".$a->getPrenom()) > ($b->getNom()." ".$b->getPrenom());});

        $text  ="";
        $text .= "<table style='width:100%;'>";
        $text .= "<thead>";
        $text .= "<tr style='border-bottom:1px solid black;'>";
        $text .= "<th>Dénomination  </th>";
        $text .= "<th>Structure de rattachement  </th>";
        $text .= "<th>Volume  </th>";
        $text .= "</tr>";
        $text .= "</thead>";
        $text .= "<tbody>";
        foreach ($formateurs as $formateur) {
            $text .= "<tr>";
            $text .= "<td>".$formateur->getPrenom()." ".$formateur->getNom()."</td>";
            $text .= "<td>".$formateur->getAttachement()."</td>";
            $text .= "<td>".(($formateur->getVolume())?:"N.C.")."</td>";
            $text .= "</tr>";
        }
        $text .= "</tbody>";
        $text .= "</table>";

        return $text;
    }

    public function getListeJournees()
    {
        /** @var FormationInstanceJournee[] $journees */
        $journees = $this->getJournees();
        //usort($journees, function (FormationInstanceJournee $a, FormationInstanceJournee $b) { return $a > $b;});

        $text  = "";
        $text .= "<table style='width:100%;'>";
        $text .= "<thead>";
        $text .= "<tr style='border-bottom:1px solid black;'>";
        $text .= "<th>Date  </th>";
        $text .= "<th>de  </th>";
        $text .= "<th>à  </th>";
        $text .= "<th>Lieu  </th>";
        $text .= "</tr>";
        $text .= "</thead>";
        $text .= "<tbody>";
        foreach($journees as $journee) {
            $text .= "<tr>";
            $text .= "<td>".$journee->getJour()."</td>";
            $text .= "<td>".$journee->getDebut()."</td>";
            $text .= "<td>".$journee->getFin()."</td>";
            $text .= "<td>".$journee->getLieu()."</td>";
            $text .= "</tr>";
        }
        $text .= "</tbody>";
        $text .= "</table>";

        return $text;
    }

    public function getListeComplementaireAgents()
    {
        /** @var FormationInstanceInscrit[] $inscrits */
        $inscrits = $this->getListeComplementaire();
        $inscrits = array_filter($inscrits, function(FormationInstanceInscrit $a) { return $a->estNonHistorise();});
        usort($inscrits, function (FormationInstanceInscrit $a, FormationInstanceInscrit $b){ return ($a->getAgent()->getNomUsuel()." ".$a->getAgent()->getPrenom()) > ($b->getAgent()->getNomUsuel()." ".$b->getAgent()->getPrenom());});

        $text  = "";
        $text .= "<table style='width:100%;'>";
        $text .= "<thead>";
        $text .= "<tr style='border-bottom:1px solid black;'>";
        $text .= "<th>Dénomination  </th>";
        $text .= "<th>Affectation principale  </th>";
        $text .= "<th>Adresse électronique  </th>";
        $text .= "</tr>";
        $text .= "</thead>";
        $text .= "<tbody>";
        foreach($inscrits as $inscrit) {
            $text .= "<tr>";
            $text .= "<td>".$inscrit->getAgent()->getNomUsuel()." ".$inscrit->getAgent()->getPrenom()."</td>";
            $text .= "<td>".(($inscrit->getAgent()->getAffectationPrincipale() AND $inscrit->getAgent()->getAffectationPrincipale()->getStructure())?$inscrit->getAgent()->getAffectationPrincipale()->getStructure()->getLibelleLong():"N.C.")."</td>";
            $text .= "<td>".(($inscrit->getAgent()->getUtilisateur())?$inscrit->getAgent()->getUtilisateur()->getEmail():"N.C.")."</td>";
            $text .= "</tr>";
        }
        $text .= "</tbody>";
        $text .= "</table>";

        return $text;
    }

    public function getDuree()
    {
        $sum = DateTime::createFromFormat('d/m/Y H:i','01/01/1970 00:00');
        /** @var FormationInstanceJournee[] $journees */
        $journees = array_filter($this->journees->toArray(), function (FormationInstanceJournee $a) { return $a->estNonHistorise(); });
        foreach ($journees as $journee) {
            $debut = DateTime::createFromFormat('d/m/Y H:i',$journee->getJour()." ".$journee->getDebut());
            $fin = DateTime::createFromFormat('d/m/Y H:i',$journee->getJour()." ".$journee->getFin());
            $duree = $fin->diff($debut);
            $sum->add($duree);
        }

        $result = $sum->diff(DateTime::createFromFormat('d/m/Y H:i','01/01/1970 00:00'));
        $heures = ($result->d * 24 + $result->h);
        $minutes = ($result->i);
        $text = $heures . " heures " . (($minutes !== 0)?($minutes." minutes"):"");
        return $text;
    }
}