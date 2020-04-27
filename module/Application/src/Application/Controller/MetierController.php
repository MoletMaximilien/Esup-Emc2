<?php

namespace Application\Controller;

use Application\Entity\Db\Domaine;
use Application\Entity\Db\FamilleProfessionnelle;
use Application\Entity\Db\Metier;
use Application\Form\ModifierLibelle\ModifierLibelleForm;
use Application\Form\ModifierLibelle\ModifierLibelleFormAwareTrait;
use Application\Form\RessourceRh\DomaineForm;
use Application\Form\RessourceRh\DomaineFormAwareTrait;
use Application\Form\RessourceRh\MetierForm;
use Application\Form\RessourceRh\MetierFormAwareTrait;
use Application\Service\Domaine\DomaineServiceAwareTrait;
use Application\Service\FamilleProfessionnelle\FamilleProfessionnelleServiceAwareTrait;
use Application\Service\Metier\MetierServiceAwareTrait;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MetierController extends AbstractActionController {
    use DomaineServiceAwareTrait;
    use FamilleProfessionnelleServiceAwareTrait;
    use MetierServiceAwareTrait;

    use DomaineFormAwareTrait;
    use MetierFormAwareTrait;
    use ModifierLibelleFormAwareTrait;

    public function indexAction() {
        $familles = $this->getFamilleProfessionnelleService()->getFamillesProfessionnelles();
        $domaines = $this->getDomaineService()->getDomaines();
        $metiers = $this->getMetierService()->getMetiers();

        return new ViewModel([
            'metiers' => $metiers,
            'familles' => $familles,
            'domaines' => $domaines,
        ]);
    }

    /** FAMILLE PROFESSIONNELLE ***************************************************************************************/

    public function ajouterFamilleAction()
    {
        $famille = new FamilleProfessionnelle();

        /** @var ModifierLibelleForm $form */
        $form = $this->getModifierLibelleForm();
        $form->setAttribute('action', $this->url()->fromRoute('metier/ajouter-famille', [], [], true));
        $form->bind($famille);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getFamilleProfessionnelleService()->create($famille);
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => 'Ajouter une nouvelle famille de métiers',
            'form' => $form,
        ]);
        return $vm;
    }

    public function modifierFamilleAction()
    {
        $famille = $this->getFamilleProfessionnelleService()->getRequestedFamilleProfessionnelle($this);

        /** @var ModifierLibelleForm $form */
        $form = $this->getModifierLibelleForm();
        $form->setAttribute('action', $this->url()->fromRoute('metier/modifier-famille', ['famille' => $famille->getId()], [], true));
        $form->bind($famille);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getFamilleProfessionnelleService()->update($famille);
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => 'Modifier une famille de métiers',
            'form' => $form,
        ]);
        return $vm;
    }

    public function historiserFamilleAction()
    {
        $famille = $this->getFamilleProfessionnelleService()->getRequestedFamilleProfessionnelle($this);

        if ($famille !== null) {
            $this->getFamilleProfessionnelleService()->historise($famille);
        }

        return $this->redirect()->toRoute('metier', [], ['fragment'=>'famille'], true);
    }

    public function restaurerFamilleAction()
    {
        $famille = $this->getFamilleProfessionnelleService()->getRequestedFamilleProfessionnelle($this);

        if ($famille !== null) {
            $this->getFamilleProfessionnelleService()->restore($famille);
        }

        return $this->redirect()->toRoute('metier', [], ['fragment'=>'famille'], true);
    }

    public function effacerFamilleAction()
    {
        $famille = $this->getFamilleProfessionnelleService()->getRequestedFamilleProfessionnelle($this);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if ($data["reponse"] === "oui") $this->getFamilleProfessionnelleService()->delete($famille);
            //return $this->redirect()->toRoute('home');
            exit();
        }

        $vm = new ViewModel();
        if ($famille !== null) {
            $vm->setTemplate('application/default/confirmation');
            $vm->setVariables([
                'title' => "Suppression de la famille professionnelle" . $famille->getLibelle(),
                'text' => "La suppression est définitive êtes-vous sûr&middot;e de vouloir continuer ?",
                'action' => $this->url()->fromRoute('metier/effacer-famille', ["famille" => $famille->getId()], [], true),
            ]);
        }
        return $vm;
    }

    /** DOMAINE *******************************************************************************************************/

    public function ajouterDomaineAction()
    {
        /** @var Domaine $domaine */
        $domaine = new Domaine();

        /** @var DomaineForm $form */
        $form = $this->getDomaineForm();
        $form->setAttribute('action', $this->url()->fromRoute('metier/ajouter-domaine', [], [], true));
        $form->bind($domaine);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getDomaineService()->create($domaine);
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => 'Ajouter un domaine',
            'form' => $form,
        ]);
        return $vm;
    }

    public function modifierDomaineAction()
    {
        $domaine = $this->getDomaineService()->getRequestedDomaine($this);

        /** @var DomaineForm $form */
        $form = $this->getDomaineForm();
        $form->setAttribute('action', $this->url()->fromRoute('metier/modifier-domaine', ['domaine' => $domaine->getId()], [], true));
        $form->bind($domaine);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getDomaineService()->update($domaine);
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => 'Modifier un domaine',
            'form' => $form,
        ]);
        return $vm;
    }

    public function historiserDomaineAction()
    {
        $domaine = $this->getDomaineService()->getRequestedDomaine($this);
        if ($domaine !== null) {
            $this->getDomaineService()->historise($domaine);
        }
        return $this->redirect()->toRoute('metier', [], ['fragment'=>'domaine'], true);
    }

    public function restaurerDomaineAction()
    {
        $domaine = $this->getDomaineService()->getRequestedDomaine($this);
        if ($domaine !== null) {
            $this->getDomaineService()->restore($domaine);
        }
        return $this->redirect()->toRoute('metier', [], ['fragment'=>'domaine'], true);
    }

    public function effacerDomaineAction()
    {
        $domaine = $this->getDomaineService()->getRequestedDomaine($this);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if ($data["reponse"] === "oui") $this->getDomaineService()->delete($domaine);
            //return $this->redirect()->toRoute('home');
            exit();
        }

        $vm = new ViewModel();
        if ($domaine !== null) {
            $vm->setTemplate('application/default/confirmation');
            $vm->setVariables([
                'title' => "Suppression du domaine" . $domaine->getLibelle(),
                'text' => "La suppression est définitive êtes-vous sûr&middot;e de vouloir continuer ?",
                'action' => $this->url()->fromRoute('metier/effacer-domaine', ["domaine" => $domaine->getId()], [], true),
            ]);
        }
        return $vm;
    }

    /** METIER ********************************************************************************************************/

    public function ajouterMetierAction()
    {
        $metier = new Metier();

        /** @var MetierForm $form */
        $form = $this->getMetierForm();
        $form->setAttribute('action', $this->url()->fromRoute('metier/ajouter-metier', [], [], true));
        $form->bind($metier);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getMetierService()->create($metier);
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => 'Ajouter un nouveau métier',
            'form' => $form,
        ]);
        return $vm;
    }

    public function modifierMetierAction()
    {
        $metier = $this->getMetierService()->getRequestedMetier($this);

        /** @var MetierForm $form */
        $form = $this->getMetierForm();
        $form->setAttribute('action', $this->url()->fromRoute('metier/modifier-metier', ['metier' => $metier->getId()], [], true));
        $form->bind($metier);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getMetierService()->update($metier);
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => 'Modifier un métier',
            'form' => $form,
        ]);
        return $vm;
    }

    public function historiserMetierAction()
    {
        $metier = $this->getMetierService()->getRequestedMetier($this);

        if ($metier !== null) {
            $this->getMetierService()->historise($metier);
        }

        return $this->redirect()->toRoute('metier', [], ["fragment" => "metier"], true);
    }

    public function restaurerMetierAction()
    {
        $metier = $this->getMetierService()->getRequestedMetier($this);

        if ($metier !== null) {
            $this->getMetierService()->restore($metier);
        }

        return $this->redirect()->toRoute('metier', [], ["fragment" => "metier"], true);
    }

    public function effacerMetierAction()
    {
        $metier = $this->getMetierService()->getRequestedMetier($this);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if ($data["reponse"] === "oui") $this->getMetierService()->delete($metier);
            //return $this->redirect()->toRoute('home');
            exit();
        }

        $vm = new ViewModel();
        if ($metier !== null) {
            $vm->setTemplate('application/default/confirmation');
            $vm->setVariables([
                'title' => "Suppression du metier " . $metier->getLibelle(),
                'text' => "La suppression est définitive êtes-vous sûr&middot;e de vouloir continuer ?",
                'action' => $this->url()->fromRoute('metier/effacer-metier', ["metier" => $metier->getId()], [], true),
            ]);
        }
        return $vm;
    }

}
