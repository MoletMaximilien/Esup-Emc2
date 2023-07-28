<?php

namespace FicheMetier\Entity\Db;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Element\Entity\Db\ApplicationElement;
use Element\Entity\Db\Competence;
use Element\Entity\Db\CompetenceElement;
use Element\Entity\Db\CompetenceType;
use Element\Entity\Db\Interfaces\HasApplicationCollectionInterface;
use Element\Entity\Db\Interfaces\HasCompetenceCollectionInterface;
use Element\Entity\Db\Traits\HasApplicationCollectionTrait;
use Element\Entity\Db\Traits\HasCompetenceCollectionTrait;
use Metier\Entity\HasMetierInterface;
use Metier\Entity\HasMetierTrait;
use UnicaenEtat\Entity\Db\HasEtatsInterface;
use UnicaenEtat\Entity\Db\HasEtatsTrait;
use UnicaenUtilisateur\Entity\Db\HistoriqueAwareInterface;
use UnicaenUtilisateur\Entity\Db\HistoriqueAwareTrait;

class FicheMetier implements HistoriqueAwareInterface, HasEtatsInterface, HasMetierInterface,
    HasApplicationCollectionInterface, HasCompetenceCollectionInterface {
    use HistoriqueAwareTrait;
    use HasMetierTrait;
    use HasEtatsTrait;
    use HasApplicationCollectionTrait;
    use HasCompetenceCollectionTrait;

    private ?int $id = -1;
    private ?bool $hasExpertise = false;
    private ?string $raison = null;

    private Collection $activites;
    private Collection $missions;

    public function __construct()
    {
        $this->activites = new ArrayCollection();
        $this->missions = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->competences = new ArrayCollection();
    }

    public function getId() : ?int
    {
        return $this->id;
    }

    public function hasExpertise() : bool
    {
        return ($this->hasExpertise === true);
    }

    public function setExpertise(?bool $has) : void
    {
        $this->hasExpertise = $has;
    }

    public function getRaison(): ?string
    {
        return $this->raison;
    }

    public function setRaison(?string $raison): void
    {
        $this->raison = $raison;
    }

    /** @return FicheMetierMission[] */
    public function getMissions() : array
    {
        $missions =  $this->missions->toArray();
        usort($missions, function (FicheMetierMission $a, FicheMetierMission $b) { return $a->getOrdre() > $b->getOrdre();});
        return $missions;
    }

    /** FONCTION POUR MACRO *******************************************************************************************/

    /**
     * @return string
     */
    public function getActivitesFromFicheMetierAsText() : string
    {
        $texte = '<ul>';
        $missions = $this->getMissions();
        usort($missions, function (FicheMetierMission $a, FicheMetierMission $b) {return $a->getOrdre() > $b->getOrdre();});
        foreach ($missions as $activite) {
            $texte .= '<li>'.$activite->getMission()->getLibelle().'</li>';
        }
        $texte .= '</ul>';
        return $texte;
    }

    public function getIntitule() : string
    {
        $metier = $this->getMetier();
        if ($metier === null) return "Aucun métier est associé.";
        return $metier->getLibelle();
    }

    /**
     * Utiliser dans la macro FICHE_METIER#MISSIONS_PRINCIPALES
     * @noinspection PhpUnused
     */
    public function getMissionsAsList() : string
    {
        $texte = "";
        foreach ($this->getMissions() as $mission) {
            $texte .= "<h3 class='mission-principale'>" . $mission->getMission()->getLibelle() . "</h3>";
            $activites = $mission->getMission()->getActivites();
            $texte .= "<ul>";
            foreach ($activites as $activite) {
                $texte .= "<li>";
                $texte .= $activite->getLibelle();
                $texte .= "</li>";
            }
            $texte .= "</ul>";
        }
        return $texte;
    }

    public function getCompetences() : string
    {
        $competences = $this->getCompetenceListe();

        $texte = "<ul>";
        foreach ($competences as $competence) {
            $texte .= "<li>";
            $texte .= $competence->getCompetence()->getLibelle();
            $texte .= "</li>";
        }
        $texte .= "</ul>";
        return $texte;
    }

    public function getComptencesByType(int $typeId) : string
    {
        $competences = $this->getCompetenceListe();
        $competences = array_map(function (CompetenceElement $c) { return $c->getCompetence();}, $competences);
        $competences = array_filter($competences, function (Competence $c) use ($typeId) { return $c->getType()->getId() === $typeId;});
        usort($competences, function (Competence $a, Competence $b) { return $a->getLibelle() > $b->getLibelle();});

        if (empty($competences)) return "";

        $competence = $competences[0];
        $competenceType = "";
        switch($competence->getType()->getId()) {
            case 1 : $competenceType = "Compétences comportementales"; break;
            case 2 : $competenceType = "Compétences opérationnelles"; break;
            case 3 : $competenceType = "Connaissances"; break;
        }

        $texte = "<h3>".$competenceType."</h3>";
        $texte .= "<ul>";
        foreach ($competences as $competence) {
            $texte .= "<li>";
            $texte .= $competence->getLibelle();
            $texte .= "</li>";
        }
        $texte .= "</ul>";
        return $texte;
    }

    public function getConnaissances() : string
    {
        return $this->getComptencesByType(CompetenceType::CODE_CONNAISSANCE);
    }

    public function getCompetencesOperationnelles() : string
    {
        return $this->getComptencesByType(CompetenceType::CODE_OPERATIONNELLE);
    }

    public function getCompetencesComportementales() : string
    {
        return $this->getComptencesByType(CompetenceType::CODE_COMPORTEMENTALE);
    }

    public function getApplicationsAffichage() : string
    {
        $applications = $this->getApplicationListe();

        $texte = "<ul>";
        /** @var ApplicationElement $applicationElement */
        foreach ($applications as $applicationElement) {
            $application = $applicationElement->getApplication();
            $texte .= "<li>".$application->getLibelle()."</li>";
        }
        $texte .= "</ul>";
        return $texte;
    }
}