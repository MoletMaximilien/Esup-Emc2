<?php

namespace Application\Entity\Db\MacroContent;

use Application\Entity\Db\Agent;

/**
 * Trait AgentMacroTrait
 */

trait AgentMacroTrait
{

    public function toStringPrenom() : string
    {
        /** @var Agent $agent */
        $agent = $this;
        if ($agent->getPrenom()) return $agent->getPrenom();
        return "Aucun prénom de renseigné";
    }

    public function toStringNomUsage() : string
    {
        /** @var Agent $agent */
        $agent = $this;
        if ($agent->getNomUsuel()) return $agent->getNomUsuel();
        return "Aucun nom d'usage de renseigné";
    }

    public function toStringNomFamille() : string
    {
        /** @var Agent $agent */
        $agent = $this;
        if ($agent->getNomFamille()) return $agent->getNomFamille();
        return "Aucun nom de famille de renseigné";
    }

    /**
     * @return string
     */
    public function toStringDenomination(): string
    {
        /** @var Agent $agent */
        $agent = $this;
        $nomUsuel = $agent->getNomUsuel();
        $prenom = $agent->getPrenom();
        return $prenom . " " . $nomUsuel;
    }

    /**
     * @return string
     */
    public function toStringAffectationsActives() : string
    {
        /** @var Agent $agent */
        $agent = $this;
        $affecations = $agent->getAffectationsActifs();
        $texte  = "<ul>";
        foreach ($affecations as $affectation) {
            $texte .= "<li>";
            $texte .= $affectation->getStructure()->toStringDenomination();
            $texte .= " (";
            if($affectation->getDateFin()) {
                $texte .= "du " . $affectation->getDateDebut()->format('d/m/Y') . " au " . $affectation->getDateFin()->format('d/m/Y');
            } else {
                $texte .= "depuis le " . $affectation->getDateDebut()->format('d/m/Y');
            }
            $texte .= ")";
            if ($affectation->isPrincipale()) {
                $texte .= "<br/>";
                $texte .= "&nbsp;&nbsp;&nbsp;&nbsp;<span class='complement'>";
                $texte .= "Affectation principale";
                $texte .= "</span>";
            }
            $texte .= "</li>";
        }
        $texte .= "</ul>";
        return $texte;
    }

    /**
     * @return string
     */
    public function toStringStatutsActifs() : string
    {
        /** @var Agent $agent */
        $agent = $this;
        $statuts = $agent->getStatutsActifs();
        $texte  = "<ul>";
        foreach ($statuts as $statut) {
            $temoins = [];
            if ($statut->isTitulaire()) $temoins[] = "Titulaire";
            if ($statut->isCdi()) $temoins[] = "C.D.I.";
            if ($statut->isCdd()) $temoins[] = "C.D.D.";
            if (!empty($temoins)) {
                $texte .= "<li>";
                $texte .= implode(", ",$temoins);
                $texte .= " (";
                if($statut->getDateFin()) {
                    $texte .= "du " . $statut->getDateDebut()->format('d/m/Y') . " au " . $statut->getDateFin()->format('d/m/Y');
                } else {
                    $texte .= "depuis le " . $statut->getDateDebut()->format('d/m/Y');
                }
                $texte .= " - " . $statut->getStructure()->getLibelleCourt() .")";
                $texte .= "</span>";
                $texte .= "</li>";
            }
        }
        $texte .= "</ul>";
        return $texte;
    }

    /**
     * @return string
     */
    public function toStringGradesActifs() : string
    {
        /** @var Agent $agent */
        $agent = $this;
        $grades = $agent->getGradesActifs();
        $texte  = "<ul>";
        foreach ($grades as $grade) {
            $texte .= "<li>";
            $grade_libelle = $grade->getCorps()->getLibelleLong();
            $grade_bap = ($grade->getBap() !== null)?$grade->getBap()->getCategorie():"";
            $texte .= $grade_libelle . " ". $grade_bap;
            $texte .= " (";
            if($grade->estFini()) {
                $texte .= "du " . $grade->getDateDebut()->format('d/m/Y') . " au " . $grade->getDateFin()->format('d/m/Y');
            } else {
                $texte .= "depuis le " . (($grade->getDateDebut() !== null)?$grade->getDateDebut()->format('d/m/Y'):"---");
            }
            $texte .= " - " . $grade->getStructure()->getLibelleCourt() .")";
            $texte .= "</span>";
            $texte .= "</li>";
        }
        $texte .= "</ul>";
        return $texte;
    }

    /**
     * @return string
     */
    public function toStringQuotiteTravaillee() : string
    {
        /** @var Agent $agent */
        $agent = $this;
        $texte = ($agent->getQuotiteCourante())?$agent->getQuotiteCourante()->getQuotite()."%":"100%";
        return $texte;
    }
    /**
     * TODO a remplacer lorsque l'on aura les macros de macro
     * @return string
     */
    public function toStringAgentBloc() : string
    {
        $texte  = "";
        $texte .= "<table class='agent-bloc'>";
        $texte .= "<tr>";
        $texte .= "<th>Dénomination</th>";
        $texte .= "<td>".$this->toStringDenomination()."</td>";
        $texte .= "</tr>";
        $texte .= "<tr>";
        $texte .= "<th> Affectation(s) </th>";
        $texte .= "<td>".$this->toStringAffectationsActives()."</td>";
        $texte .= "</tr>";
        $texte .= "<tr>";
        $texte .= "<th> Statut(s) </th>";
        $texte .= "<td>".$this->toStringStatutsActifs()."</td>";
        $texte .= "</tr>";
        $texte .= "<tr>";
        $texte .= "<th> Grade(s) </th>";
        $texte .= "<td>".$this->toStringGradesActifs()."</td>";
        $texte .= "</tr>";
        $texte .= "<tr>";
        $texte .= "<th> Quotité travaillée </th>";
        $texte .= "<td>".$this->toStringQuotiteTravaillee()."</td>";
        $texte .= "</tr>";
        $texte .= "</table>";
        return $texte;
    }

    /**
     * TODO a remplacer lorsque l'on aura les macros de macro
     * @return string
     */
    public function toStringStructuresBloc() : string
    {
        /** @var Agent $agent */
        $agent = $this;
        $structures = $agent->getStructures();

        $texte = "";
        foreach ($structures as $structure) {
            if ($texte !== "") $texte .= "<br/>";
            $texte .= $structure->toStringStructureBloc();
        }
        return $texte;
    }

    public function toStringDateNaissance() : string
    {
        /** @var Agent $agent */
        $agent = $this;

        if ($agent->getDateNaissance()) {
            return $agent->getDateNaissance()->format('d/m/Y');
        }
        return "Date de naissance non renseignée";
    }

}