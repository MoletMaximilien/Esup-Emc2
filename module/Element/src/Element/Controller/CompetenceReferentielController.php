<?php

namespace Element\Controller;

use Element\Entity\Db\CompetenceReferentiel;
use Element\Form\CompetenceReferentiel\CompetenceReferentielFormAwareTrait;
use Element\Service\CompetenceReferentiel\CompetenceReferentielServiceAwareTrait;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class CompetenceReferentielController extends AbstractActionController
{
    use CompetenceReferentielServiceAwareTrait;
    use CompetenceReferentielFormAwareTrait;

    public function indexAction(): ViewModel
    {
        $referentiels = $this->getCompetenceReferentielService()->getCompetencesReferentiels();

        return new ViewModel([
            'referentiels' => $referentiels,
        ]);
    }

    public function afficherAction(): ViewModel
    {
        $referentiel = $this->getCompetenceReferentielService()->getRequestedCompetenceReferentiel($this);
        return new ViewModel([
            'title' => "Affichage d'un référentiel de compétences",
            'referentiel' => $referentiel,
        ]);
    }

    public function ajouterAction(): ViewModel
    {
        $referentiel = new CompetenceReferentiel();
        $form = $this->getCompetenceReferentielForm();
        $form->setAttribute('action', $this->url()->fromRoute('element/competence-referentiel/ajouter', [], [], true));
        $form->bind($referentiel);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getCompetenceReferentielService()->create($referentiel);
                exit();
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('default/default-form');
        $vm->setVariables([
            'title' => "Ajout d'un référentiel de compétences",
            'form' => $form,
        ]);
        return $vm;
    }

    public function modifierAction(): ViewModel
    {
        $referentiel = $this->getCompetenceReferentielService()->getRequestedCompetenceReferentiel($this);
        $form = $this->getCompetenceReferentielForm();
        $form->setAttribute('action', $this->url()->fromRoute('element/competence-referentiel/modifier', ['competence-referentiel' => $referentiel->getId()], [], true));
        $form->bind($referentiel);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getCompetenceReferentielService()->update($referentiel);
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('default/default-form');
        $vm->setVariables([
            'title' => "Modification d'un référentiel de compétences",
            'form' => $form,
        ]);
        return $vm;
    }

    public function historiserAction(): Response
    {
        $referentiel = $this->getCompetenceReferentielService()->getRequestedCompetenceReferentiel($this);
        $this->getCompetenceReferentielService()->historise($referentiel);

        $retour = $this->params()->fromQuery('retour');
        if ($retour) return $this->redirect()->toUrl($retour);
        return $this->redirect()->toRoute('element/competence-referentiel', [], [], true);
    }

    public function restaurerAction(): Response
    {
        $referentiel = $this->getCompetenceReferentielService()->getRequestedCompetenceReferentiel($this);
        $this->getCompetenceReferentielService()->restore($referentiel);

        $retour = $this->params()->fromQuery('retour');
        if ($retour) return $this->redirect()->toUrl($retour);
        return $this->redirect()->toRoute('element/competence-referentiel', [], [], true);
    }

    public function supprimerAction(): ViewModel
    {
        $referentiel = $this->getCompetenceReferentielService()->getRequestedCompetenceReferentiel($this);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if ($data["reponse"] === "oui") $this->getCompetenceReferentielService()->delete($referentiel);
            exit();
        }

        $vm = new ViewModel();
        if ($referentiel !== null) {
            $vm->setTemplate('default/confirmation');
            $vm->setVariables([
                'title' => "Suppression du référentiel de compétences  " . $referentiel->getLibelleCourt(),
                'text' => "La suppression est définitive êtes-vous sûr&middot;e de vouloir continuer ?",
                'action' => $this->url()->fromRoute('element/competence-referentiel/supprimer', ["competence-referentiel" => $referentiel->getId()], [], true),
            ]);
        }
        return $vm;
    }
}