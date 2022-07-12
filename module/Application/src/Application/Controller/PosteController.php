<?php

namespace Application\Controller;

use Application\Entity\Db\Poste;
use Application\Form\Poste\PosteForm;
use Application\Form\Poste\PosteFormAwareTrait;
use Application\Service\Poste\PosteServiceAwareTrait;
use Laminas\Http\Request;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class PosteController extends AbstractActionController {
    use PosteServiceAwareTrait;
    use PosteFormAwareTrait;

    public function indexAction() : ViewModel
    {
        $postes = $this->getPosteService()->getPostes();
        
        return new ViewModel([
            'postes' => $postes,
        ]);
    }

    public function afficherAction()  :ViewModel
    {
        /** @var Poste $poste */
        $posteId = $this->params()->fromRoute('poste');
        $poste = $this->getPosteService()->getPoste($posteId);

        return new ViewModel([
            'title' => 'Affichage du poste',
            'poste' => $poste,
        ]);
    }

    public function ajouterAction() : ViewModel
    {
        /** @var Poste $poste */
        $poste = new Poste();

        /** @var PosteForm $form */
        $form = $this->getPosteForm();
        $form->setAttribute('action', $this->url()->fromRoute('poste/ajouter', [], [], true));
        $form->bind($poste);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getPosteService()->create($poste);
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => "Ajouter un poste",
            'form' => $form,
        ]);
        return $vm;
    }

    public function modifierAction() : ViewModel
    {
        /** @var Poste $poste */
        $posteId = $this->params()->fromRoute('poste');
        $poste = $this->getPosteService()->getPoste($posteId);

        /** @var PosteForm $form */
        $form = $this->getPosteForm();
        $form->setAttribute('action', $this->url()->fromRoute('poste/modifier', ['poste' => $poste->getId()], [], true));
        $form->bind($poste);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getPosteService()->update($poste);
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => "Modifier un poste",
            'form' => $form,
        ]);
        return $vm;
    }

    public function supprimerAction() : ViewModel
    {
        $posteId = $this->params()->fromRoute('poste');
        $poste = $this->getPosteService()->getPoste($posteId);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if ($data["reponse"] === "oui") $this->getPosteService()->delete($poste);
            exit();
        }

        $vm = new ViewModel();
        if ($poste !== null) {
            $vm->setTemplate('application/default/confirmation');
            $vm->setVariables([
                'title' => "Suppression du poste [" . $poste->getNumeroPoste() ."]",
                'text' => "La suppression est définitive êtes-vous sûr&middot;e de vouloir continuer ?",
                'action' => $this->url()->fromRoute('poste/supprimer', ["poste" => $poste->getId()], [], true),
            ]);
        }
        return $vm;
    }
}