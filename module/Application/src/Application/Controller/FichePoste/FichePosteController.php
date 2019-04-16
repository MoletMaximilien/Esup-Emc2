<?php

namespace Application\Controller\FichePoste;

use Application\Entity\Db\FicheMetierType;
use Application\Entity\Db\FichePoste;
use Application\Entity\Db\FicheTypeExterne;
use Application\Entity\Db\SpecificitePoste;
use Application\Form\AjouterFicheMetier\AjouterFicheMetierFormAwareTrait;
use Application\Form\AssocierAgent\AssocierAgentForm;
use Application\Form\AssocierAgent\AssocierAgentFormAwareTrait;
use Application\Form\AssocierPoste\AssocierPosteFormAwareTrait;
use Application\Form\FichePosteCreation\FichePosteCreationForm;
use Application\Form\FichePosteCreation\FichePosteCreationFormAwareTrait;
use Application\Form\SpecificitePoste\SpecificitePosteForm;
use Application\Form\SpecificitePoste\SpecificitePosteFormAwareTrait;
use Application\Service\Agent\AgentServiceAwareTrait;
use Application\Service\FichePoste\FichePosteServiceAwareTrait;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FichePosteController extends AbstractActionController {
    /** Service **/
    use AgentServiceAwareTrait;
    use FichePosteServiceAwareTrait;
    /** Form **/
    use AjouterFicheMetierFormAwareTrait;
    use AssocierAgentFormAwareTrait;
    use FichePosteCreationFormAwareTrait;
    use AssocierPosteFormAwareTrait;
    use SpecificitePosteFormAwareTrait;

    public function indexAction()
    {
        $fiches = $this->getFichePosteService()->getFichesPostes();

        return new ViewModel([
            'fiches' => $fiches,
        ]);
    }

    public function ajouterAction()
    {
        $fiche = new FichePoste();
        $form = $this->getFichePosteCreationForm();
        $form->setAttribute('action', $this->url()->fromRoute('fiche-poste/ajouter', ['fiche-poste' => $fiche->getId()], [], true));
        $form->bind($fiche);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $fiche = $this->getFichePosteService()->create($fiche);
                //$this->redirect()->toRoute('fiche-metier/editer', ['fiche-poste' => $fiche->getId()], [], true);
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => 'Libelle de la fiche de poste',
            'form' => $form,
        ]);
        return $vm;
    }


    public function afficherAction()
    {
        $fiche = $this->getFichePosteService()->getRequestedFichePoste($this, 'fiche-poste');
        return new ViewModel([
            'title' => 'Fiche de poste <br/> <em>'.$fiche->getLibelle().'</em>',
           'fiche' => $fiche,
        ]);
    }

    public function editerAction()
    {
        $fiche = $this->getFichePosteService()->getRequestedFichePoste($this, 'fiche-poste');
        return new ViewModel([
            'fiche' => $fiche,
        ]);
    }

    public function historiserAction()
    {
        $fiche = $this->getFichePosteService()->getRequestedFichePoste($this, 'fiche-poste');
        $this->getFichePosteService()->historise($fiche);
        $this->redirect()->toRoute('fiche-poste', [], [], true);
    }

    public function restaurerAction()
    {
        $fiche = $this->getFichePosteService()->getRequestedFichePoste($this, 'fiche-poste');
        $this->getFichePosteService()->restore($fiche);
        $this->redirect()->toRoute('fiche-poste', [], [], true);
    }

    public function detruireAction()
    {
        $fiche = $this->getFichePosteService()->getRequestedFichePoste($this, 'fiche-poste');
        $this->getFichePosteService()->delete($fiche);
        $this->redirect()->toRoute('fiche-poste', [], [], true);
    }

    /** AGENT *********************************************************************************************************/

    public function associerAgentAction()
    {
        $fiche = $this->getFichePosteService()->getRequestedFichePoste($this, 'fiche-poste');

        /** @var AssocierAgentForm $form */
        $form = $this->getAssocierAgentForm();
        $form->setAttribute('action', $this->url()->fromRoute('fiche-poste/associer-agent', ['fiche-poste' => $fiche->getId()], [], true));
        $form->bind($fiche);

        /**@var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getFichePosteService()->update($fiche);
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => 'Associer un agent',
            'form' => $form,
            'agents' => $this->getAgentService()->getAgents(),
        ]);
        return $vm;
    }

    /** POSTE *********************************************************************************************************/

    public function associerPosteAction()
    {
        $fiche = $this->getFichePosteService()->getRequestedFichePoste($this, 'fiche-poste');

        /** @var AssocierAgentForm $form */
        $form = $this->getAssocierPosteForm();
        $form->setAttribute('action', $this->url()->fromRoute('fiche-poste/associer-poste', ['fiche-poste' => $fiche->getId()], [], true));
        $form->bind($fiche);

        /**@var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getFichePosteService()->update($fiche);
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => 'Associer un poste',
            'form' => $form,
        ]);
        return $vm;

    }

    /** FICHE METIER **************************************************************************************************/

    public function ajouterFicheMetierAction()
    {
        $fiche = $this->getFichePosteService()->getRequestedFichePoste($this, 'fiche-poste');

        $ficheTypeExterne = new FicheTypeExterne();
        $form = $this->getAjouterFicheTypeForm();
        $form->setAttribute('action', $this->url()->fromRoute('fiche-poste/ajouter-fiche-metier', ['fiche-poste' => $fiche->getId()], [], true));
        $form->bind($ficheTypeExterne);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $ficheTypeExterne->setFichePoste($fiche);
                $this->getFichePosteService()->createFicheTypeExterne($ficheTypeExterne);

                if ($ficheTypeExterne->getPrincipale()) {
                    foreach ($fiche->getFichesMetiers() as $ficheMetier) {
                        if ($ficheMetier !== $ficheTypeExterne && $ficheMetier->getPrincipale()) {
                            $ficheMetier->setPrincipale(false);
                            //$this->getFichePosteService()->updateFicheTypeExterne($ficheMetier);
                        }
                    }
                }

                //comportement par defaut (ajout de toutes les activités)
                /** @var FicheMetierType */
                $activites = $ficheTypeExterne->getFicheType()->getActivites();
                $tab = [];
                foreach ($activites as $activite) {
                    $tab[] = $activite->getActivite()->getId();
                }
                $text = implode(";",$tab);
                $ficheTypeExterne->setActivites($text);
                $this->getFichePosteService()->updateFicheTypeExterne($ficheTypeExterne);

            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => 'Ajout d\'une fiche type',
            'form'  => $form,
        ]);
        return $vm;
    }

    public function retirerFicheMetierAction()
    {
        $fichePoste = $this->getFichePosteService()->getRequestedFichePoste($this, 'fiche-poste');
        $ficheTypeExterneId = $this->params()->fromRoute('fiche-type-externe');
        $ficheTypeExterne = $this->getFichePosteService()->getFicheTypeExterne($ficheTypeExterneId);

        if ($ficheTypeExterne && $fichePoste) $this->getFichePosteService()->deleteFicheTypeExterne($ficheTypeExterne);

        $this->redirect()->toRoute('fiche-poste/editer',['fiche-poste' => $fichePoste->getId()], [], true);
    }

    public function modifierFicheMetierAction()
    {
        $fichePoste = $this->getFichePosteService()->getRequestedFichePoste($this, 'fiche-poste');
        $ficheTypeExterneId = $this->params()->fromRoute('fiche-type-externe');
        $ficheTypeExterne = $this->getFichePosteService()->getFicheTypeExterne($ficheTypeExterneId);

        $form = $this->getAjouterFicheTypeForm();
        $form->setAttribute('action', $this->url()->fromRoute('fiche-poste/modifier-fiche-metier', ['fiche-poste' => $fichePoste->getId(), 'fiche-type-externe' => $ficheTypeExterne->getId()], [], true));
        $form->bind($ficheTypeExterne);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $ficheTypeExterne->setFichePoste($fichePoste);
                $this->getFichePosteService()->updateFicheTypeExterne($ficheTypeExterne);

                if ($ficheTypeExterne->getPrincipale()) {
                    foreach ($fichePoste->getFichesMetiers() as $ficheMetier) {
                        if ($ficheMetier !== $ficheTypeExterne && $ficheMetier->getPrincipale()) {
                            $ficheMetier->setPrincipale(false);
                            $this->getFichePosteService()->updateFicheTypeExterne($ficheMetier);
                        }
                    }
                }
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => 'Modification d\'une fiche type',
            'form'  => $form,
        ]);
        return $vm;
    }


    public function selectionnerActiviteAction()
    {
        $fichePoste = $this->getFichePosteService()->getRequestedFichePoste($this, 'fiche-poste');
        $ficheTypeExterneId = $this->params()->fromRoute('fiche-type-externe');
        $ficheTypeExterne = $this->getFichePosteService()->getFicheTypeExterne($ficheTypeExterneId);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();

            $result = [];
            foreach ($data as $key => $value) {
                if ($value === 'on') $result[] = $key;
            }
            $result = implode(";", $result);
            $ficheTypeExterne->setActivites($result);
            $this->getFichePosteService()->updateFicheTypeExterne($ficheTypeExterne);
        }

        return new ViewModel([
            'title' => 'Liste des activités de la fiche type <br/> <strong>'. $ficheTypeExterne->getFicheType()->getMetier() .'</strong>',
            'fichePoste' => $fichePoste,
            'ficheTypeExterne' => $ficheTypeExterne,
        ]);
    }

    /** SPECIFICITE ***************************************************************************************************/

    public function editerSpecificiteAction()
    {
        $fiche = $this->getFichePosteService()->getRequestedFichePoste($this, 'fiche-poste');

        $specificite = $fiche->getSpecificite();
        if ($specificite === null) {
            $specificite = new SpecificitePoste();
            $fiche->setSpecificite($specificite);
            $this->getFichePosteService()->createSpecificitePoste($specificite);
        }

        /** @var SpecificitePosteForm $form */
        $form = $form = $this->getSpecificitePosteForm();
        $form->setAttribute('action', $this->url()->fromRoute('fiche-poste/editer-specificite', ['fiche' => $fiche->getId()], [], true));
        $form->bind($specificite);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $specificite->setFiche($fiche);
                $this->getFichePosteService()->updateSpecificitePoste($specificite);
                $this->getFichePosteService()->update($fiche);
            }
        }

        return new ViewModel([
            'title' => 'Éditer spécificité du poste',
            'form' => $form,
        ]);

    }
}