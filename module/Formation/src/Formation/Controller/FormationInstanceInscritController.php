<?php

namespace Formation\Controller;

use Application\Entity\Db\Interfaces\HasSourceInterface;
use Application\Form\SelectionAgent\SelectionAgentFormAwareTrait;
use Application\Service\Agent\AgentServiceAwareTrait;
use Formation\Entity\Db\DemandeExterne;
use Formation\Entity\Db\Formation;
use Formation\Entity\Db\FormationInstanceInscrit;
use Formation\Form\Inscription\InscriptionFormAwareTrait;
use Formation\Provider\Etat\DemandeExterneEtats;
use Formation\Provider\Etat\InscriptionEtats;
use Formation\Provider\Etat\SessionEtats;
use Formation\Provider\Parametre\FormationParametres;
use Formation\Service\DemandeExterne\DemandeExterneServiceAwareTrait;
use Formation\Service\FormationInstance\FormationInstanceServiceAwareTrait;
use Formation\Service\FormationInstanceInscrit\FormationInstanceInscritServiceAwareTrait;
use Formation\Service\Notification\NotificationServiceAwareTrait;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use UnicaenApp\Form\Element\SearchAndSelect;
use UnicaenEtat\Service\EtatInstance\EtatInstanceServiceAwareTrait;
use UnicaenEtat\Service\EtatType\EtatTypeServiceAwareTrait;
use UnicaenMail\Service\Mail\MailServiceAwareTrait;
use UnicaenParametre\Service\Parametre\ParametreServiceAwareTrait;
use UnicaenRenderer\Service\Rendu\RenduServiceAwareTrait;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

/** @method FlashMessenger flashMessenger() */

class FormationInstanceInscritController extends AbstractActionController
{
    use AgentServiceAwareTrait;
    use EtatInstanceServiceAwareTrait;
    use EtatTypeServiceAwareTrait;
    use DemandeExterneServiceAwareTrait;
    use FormationInstanceServiceAwareTrait;
    use FormationInstanceInscritServiceAwareTrait;
    use MailServiceAwareTrait;
    use NotificationServiceAwareTrait;
    use ParametreServiceAwareTrait;
    use RenduServiceAwareTrait;
    use UserServiceAwareTrait;

    use InscriptionFormAwareTrait;
    use SelectionAgentFormAwareTrait;




    public function afficherAgentAction() : ViewModel
    {
        $inscrit = $this->getFormationInstanceInscritService()->getRequestedFormationInstanceInscrit($this);
        return new ViewModel([
            'agent' => $inscrit->getAgent(),
            'demande' => $inscrit,
        ]);
    }

    public function ajouterAgentAction() : ViewModel
    {
        $instance = $this->getFormationInstanceService()->getRequestedFormationInstance($this);

        $inscrit = new FormationInstanceInscrit();
        $inscrit->setInstance($instance);

        $form = $this->getSelectionAgentForm();
        $form->setAttribute('action', $this->url()->fromRoute('formation-instance/ajouter-agent', ['formulaire-instance' => $instance->getId()], [], true));
        $form->bind($inscrit);

        /** @see  \Application\Controller\AgentController::rechercherLargeAction() */
        $urlLarge = $this->url()->fromRoute('agent/rechercher-large', [], [], true);
        /** @var SearchAndSelect $sas */
        $sas = $form->get('agent');
        $sas->setAutocompleteSource($urlLarge);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);

            if ($form->isValid()) {
                if (!$instance->hasAgent($inscrit->getAgent())) {
                    $inscrit->setListe($instance->getListeDisponible());
                    $inscrit->setSource(HasSourceInterface::SOURCE_EMC2);
                    $this->getFormationInstanceInscritService()->create($inscrit);
                    $this->getEtatInstanceService()->setEtatActif($inscrit,InscriptionEtats::ETAT_VALIDER_DRH);
                    $this->getFormationInstanceInscritService()->update($inscrit);

                    $texte = ($instance->getListeDisponible() === FormationInstanceInscrit::PRINCIPALE) ? "principale" : "complémentaire";
                    $this->flashMessenger()->addSuccessMessage("L'agent <strong>" . $inscrit->getAgent()->getDenomination() . "</strong> vient d'être ajouté&middot;e en <strong>liste " . $texte . "</strong>.");

                    if ($instance->getFormation()->getRattachement() === Formation::RATTACHEMENT_PREVENTION) $this->getNotificationService()->triggerPrevention($inscrit);

                } else {
                    $this->flashMessenger()->addErrorMessage("L'agent <strong>" . $inscrit->getAgent()->getDenomination() . "</strong> est déjà inscrit&middot;e à l'instance de formation.");
                }
            }
        }

        $vm = new ViewModel();
        $vm->setTemplate('application/default/default-form');
        $vm->setVariables([
            'title' => "Ajout d'un agent pour l'instance de formation",
            'form' => $form,
        ]);
        return $vm;
    }

        public function historiserAgentAction() : Response
    {
        $inscrit = $this->getFormationInstanceInscritService()->getRequestedFormationInstanceInscrit($this);
        $inscrit->setListe(null);
        $this->getFormationInstanceInscritService()->historise($inscrit);

        $this->flashMessenger()->addSuccessMessage("L'agent <strong>" . $inscrit->getAgent()->getDenomination() . "</strong> vient d'être retiré&middot;e des listes.");

        return $this->redirect()->toRoute('formation-instance/afficher', ['formation-instance' => $inscrit->getInstance()->getId()], [], true);
    }

    public function restaurerAgentAction() : Response
    {
        $inscrit = $this->getFormationInstanceInscritService()->getRequestedFormationInstanceInscrit($this);
        $liste = $inscrit->getInstance()->getListeDisponible();

        if ($liste !== null) {
            $inscrit->setListe($liste);
            $this->getFormationInstanceInscritService()->restore($inscrit);
        }
        return $this->redirect()->toRoute('formation-instance/afficher', ['formation-instance' => $inscrit->getInstance()->getId()], [], true);
    }

    public function supprimerAgentAction() : ViewModel
    {
        $inscrit = $this->getFormationInstanceInscritService()->getRequestedFormationInstanceInscrit($this);

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if ($data["reponse"] === "oui") $this->getFormationInstanceInscritService()->delete($inscrit);
            exit();
        }

        $vm = new ViewModel();
        $vm->setTemplate('default/confirmation');
        if ($inscrit !== null) {
            $vm->setVariables([
                'title' => "Suppression de l'inscription de [" . $inscrit->getAgent()->getDenomination() . "]",
                'text' => "La suppression est définitive êtes-vous sûr&middot;e de vouloir continuer ?",
                'action' => $this->url()->fromRoute('formation-instance/supprimer-agent', ["inscrit" => $inscrit->getId()], [], true),
            ]);
        }
        return $vm;
    }

    public function envoyerListePrincipaleAction() : Response
    {
        $inscrit = $this->getFormationInstanceInscritService()->getRequestedFormationInstanceInscrit($this);

        $inscrit->setListe(FormationInstanceInscrit::PRINCIPALE);
        $this->getFormationInstanceInscritService()->update($inscrit);

        $this->getNotificationService()->triggerListePrincipale($inscrit);

        $this->flashMessenger()->addSuccessMessage("L'agent <strong>" . $inscrit->getAgent()->getDenomination() . "</strong> vient d'être ajouté&middot;e en liste principale.");

        return $this->redirect()->toRoute('formation-instance/afficher', ['formation-instance' => $inscrit->getInstance()->getId()], [], true);
    }

    public function envoyerListeComplementaireAction() : Response
    {
        $inscrit = $this->getFormationInstanceInscritService()->getRequestedFormationInstanceInscrit($this);

        $inscrit->setListe(FormationInstanceInscrit::COMPLEMENTAIRE);
        $this->getFormationInstanceInscritService()->update($inscrit);

        $this->getNotificationService()->triggerListeComplementaire($inscrit);


        $this->flashMessenger()->addSuccessMessage("L'agent <strong>" . $inscrit->getAgent()->getDenomination() . "</strong> vient d'être ajouté&middot;e en liste complémentaire.");

        return $this->redirect()->toRoute('formation-instance/afficher', ['formation-instance' => $inscrit->getInstance()->getId()], [], true);
    }

    public function inscriptionInterneAction() : ViewModel
    {
        $instances = $this->getFormationInstanceService()->getFormationsInstancesByEtat(SessionEtats::ETAT_INSCRIPTION_OUVERTE);
        $utilisateur = $this->getUserService()->getConnectedUser();
        $agent = $this->getAgentService()->getAgentByUser($utilisateur);

        $inscriptions = $this->getFormationInstanceInscritService()->getFormationsByInscrit($agent);

        return new ViewModel([
            'instances' => $instances,
            'inscriptions' => $inscriptions,
            'agent' => $agent,
        ]);
    }
    public function inscriptionExterneAction() : ViewModel
    {
        $utilisateur = $this->getUserService()->getConnectedUser();
        $agent = $this->getAgentService()->getAgentByUser($utilisateur);


        $demandes = $this->getDemandeExterneService()->getDemandesExternesByAgent($agent);
        $demandes = array_filter($demandes, function (DemandeExterne $d) { return $d->estNonHistorise();});
        $demandesNonValidees = array_filter($demandes, function (DemandeExterne $d) { return $d->isEtatActif(DemandeExterneEtats::ETAT_CREATION_EN_COURS); });

        return new ViewModel([
            'agent' => $agent,
            'demandes' => $demandesNonValidees,
        ]);
    }

    public function formationsAction() : ViewModel
    {
        $utilisateur = $this->getUserService()->getConnectedUser();
        $agent = $this->getAgentService()->getAgentByUser($utilisateur);

        $formations = $this->getFormationInstanceInscritService()->getFormationsBySuivies($agent);
        $mail = $this->getParametreService()->getParametreByCode(FormationParametres::TYPE, FormationParametres::MAIL_DRH_FORMATION);

        return new ViewModel([
            'agent' => $agent,
            'formations' => $formations,
            'mailcontact' => ($mail)?$mail->getValeur():null,
        ]);
    }

    public function inscriptionsAction() : ViewModel
    {
        $utilisateur = $this->getUserService()->getConnectedUser();
        $agent = $this->getAgentService()->getAgentByUser($utilisateur);

        $inscriptions = $this->getFormationInstanceInscritService()->getFormationsByInscrit($agent);

        $demandes = $this->getDemandeExterneService()->getDemandesExternesByAgent($agent);
        $demandes = array_filter($demandes,
            function (DemandeExterne $d) {
            return     $d->estNonHistorise()
                    && !$d->isEtatActif(DemandeExterneEtats::ETAT_REJETEE)
                    && !$d->isEtatActif(DemandeExterneEtats::ETAT_TERMINEE);
            });
        $demandesValidees    = array_filter($demandes, function (DemandeExterne $d) {
            return !$d->isEtatActif(DemandeExterneEtats::ETAT_CREATION_EN_COURS); });

        return new ViewModel([
            'agent' => $agent,

            'inscriptions' => $inscriptions,
            'demandes' => $demandesValidees,
        ]);
    }

    public function inscriptionAction()  : ViewModel
    {
        $instance = $this->getFormationInstanceService()->getRequestedFormationInstance($this);
        $agent = $this->getAgentService()->getRequestedAgent($this);

        $inscription = new FormationInstanceInscrit();
        $inscription->setInstance($instance);
        $inscription->setAgent($agent);

        $form = $this->getInscriptionForm();
        $form->setAttribute('action', $this->url()->fromRoute('formation-instance/inscription', ['formation-instance' => $instance->getId(), 'agent' => $agent->getId()], [], true));
        $form->bind($inscription);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $justification = (isset($data['HasDescription']['description']) && trim($data['HasDescription']['description']) !== '')?trim($data['HasDescription']['description']):null;
                if ($justification === null) {
                    $this->flashMessenger()->addErrorMessage("<span class='text-danger'><strong> Échec de l'inscription  </strong></span> <br/> Veuillez motivier votre demande d'inscription!");
                } else {
                    $inscription->setJustificationAgent($justification);
                    $inscription->setSource(HasSourceInterface::SOURCE_EMC2);
                    $this->getFormationInstanceInscritService()->create($inscription);
                    $this->getEtatInstanceService()->setEtatActif($inscription,InscriptionEtats::ETAT_DEMANDE);
                    $this->getFormationInstanceInscritService()->update($inscription);
                    $this->flashMessenger()->addSuccessMessage("Demande d'inscription faite.");
                    $this->getNotificationService()->triggerInscriptionAgent($agent, $instance);
                }
            }
        }

        return new ViewModel([
            'title' => "Inscription à la formation ".$instance->getInstanceLibelle(). " du ".$instance->getPeriode(),
            'inscription' => $inscription,
            'form' => $form,
        ]);
    }

    public function desinscriptionAction() : ViewModel
    {
        $inscription = $this->getFormationInstanceInscritService()->getRequestedFormationInstanceInscrit($this);
        $instance = $inscription->getInstance();
        $agent = $inscription->getAgent();


        $form = $this->getInscriptionForm();
        $form->setAttribute('action', $this->url()->fromRoute('formation-instance/desinscription', ['inscrit' => $inscription->getId(), 'agent' => $agent->getId()], [], true));
        $form->bind($inscription);

        $break = false;
        if ($agent->getUtilisateur() !== $this->getUserService()->getConnectedUser()) {
            $this->flashMessenger()->addErrorMessage("L'utilisateur connecté ne correspond à l'agent en train de se déinscrire !");
            $break = true;
        }

        if (!$break) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost();
                $form->setData($data);
                if ($form->isValid()) {
                    $justification = (isset($data['HasDescription']['description']) && trim($data['HasDescription']['description']) !== '')?trim($data['HasDescription']['description']):null;
                    if ($justification === null) {
                        $this->flashMessenger()->addErrorMessage("<span class='text-danger'><strong> Échec de la désinscription  </strong></span> <br/> Veuillez justifier votre demande de désinscription !");
                    } else {
                        $inscription->setJustificationRefus($justification);
                        $this->getEtatInstanceService()->setEtatActif($inscription,InscriptionEtats::ETAT_REFUSER);
                        $this->getFormationInstanceInscritService()->historise($inscription);
                        $this->flashMessenger()->addSuccessMessage("Désinscription faite.");
                        //todo trigger reclassement
                    }
                }
            }
        }

        $vm =  new ViewModel([
            'title' => "Désinscription à la formation ".$instance->getInstanceLibelle(). " du ".$instance->getPeriode(),
            'inscription' => $inscription,
            'form' => $form,
        ]);
        $vm->setTemplate('formation/formation-instance-inscrit/inscription');
        return $vm;
    }

    /** VALIDATION ****************************************************************************************************/

    public function validerResponsableAction() : ViewModel
    {
        $inscription = $this->getFormationInstanceInscritService()->getRequestedFormationInstanceInscrit($this);
        $instance = $inscription->getInstance();
        $agent = $inscription->getAgent();

        $form = $this->getInscriptionForm();
        $form->setAttribute('action', $this->url()->fromRoute('formation-instance/valider-responsable', ['inscrit' => $inscription->getId(), 'agent' => $agent->getId()], [], true));
        $form->bind($inscription);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $justification = (isset($data['HasDescription']['description']) && trim($data['HasDescription']['description']) !== '')?trim($data['HasDescription']['description']):null;
                if ($justification === null) {
                    $this->flashMessenger()->addErrorMessage("<span class='text-danger'><strong> Échec de la validation </strong></span> <br/> Veuillez justifier votre validation !");
                } else {
                    $inscription->setJustificationResponsable($justification);
                    $this->getEtatInstanceService()->setEtatActif($inscription,InscriptionEtats::ETAT_VALIDER_RESPONSABLE);
                    $this->getFormationInstanceInscritService()->update($inscription);
                    $this->flashMessenger()->addSuccessMessage("Validation effectuée.");
                    $this->getNotificationService()->triggerResponsableValidation($inscription);
                }
            }
        }

        $vm =  new ViewModel([
            'title' => "Validation de l'inscription de ". $agent->getDenomination() ." à la formation ".$instance->getInstanceLibelle(). " du ".$instance->getPeriode(),
            'inscription' => $inscription,
            'form' => $form,
        ]);
        $vm->setTemplate('formation/formation-instance-inscrit/inscription');
        return $vm;
    }

    public function refuserResponsableAction() : ViewModel
    {
        $inscription = $this->getFormationInstanceInscritService()->getRequestedFormationInstanceInscrit($this);
        $instance = $inscription->getInstance();
        $agent = $inscription->getAgent();

        $form = $this->getInscriptionForm();
        $form->setAttribute('action', $this->url()->fromRoute('formation-instance/refuser-responsable', ['inscrit' => $inscription->getId(), 'agent' => $agent->getId()], [], true));
        $form->bind($inscription);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $justification = (isset($data['HasDescription']['description']) && trim($data['HasDescription']['description']) !== '')?trim($data['HasDescription']['description']):null;
                if ($justification === null) {
                    $this->flashMessenger()->addErrorMessage("<span class='text-danger'><strong> Échec du refus  </strong></span> <br/> Veuillez justifier votre refus !");
                } else {
                    $inscription->setJustificationRefus($justification);
                    $this->getEtatInstanceService()->setEtatActif($inscription,InscriptionEtats::ETAT_VALIDER_RESPONSABLE);
                    $this->getFormationInstanceInscritService()->historise($inscription);
                    $this->flashMessenger()->addSuccessMessage("Refus effectué.");
                    $this->getNotificationService()->triggerResponsableRefus($inscription);
                    //todo trigger reclassement
                }
            }
        }

        $vm =  new ViewModel([
            'title' => "refus de l'inscription de ". $agent->getDenomination() ." à la formation ".$instance->getInstanceLibelle(). " du ".$instance->getPeriode(),
            'inscription' => $inscription,
            'form' => $form,
        ]);
        $vm->setTemplate('formation/formation-instance-inscrit/inscription');
        return $vm;
    }

    public function validerDrhAction() : ViewModel
    {
        $inscription = $this->getFormationInstanceInscritService()->getRequestedFormationInstanceInscrit($this);
        $instance = $inscription->getInstance();
        $agent = $inscription->getAgent();

        $form = $this->getInscriptionForm();
        $form->setAttribute('action', $this->url()->fromRoute('formation-instance/valider-drh', ['inscrit' => $inscription->getId(), 'agent' => $agent->getId()], [], true));
        $form->bind($inscription);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getEtatInstanceService()->setEtatActif($inscription,InscriptionEtats::ETAT_VALIDER_DRH);
                $this->getFormationInstanceInscritService()->update($inscription);
                $this->getFormationInstanceService()->classerInscription($inscription);
                $this->flashMessenger()->addSuccessMessage("Validation effectuée.");
                $this->getNotificationService()->triggerDrhValidation($inscription);
                if ($inscription->getListe() === FormationInstanceInscrit::PRINCIPALE
                    AND $inscription->getInstance()->getFormation()->getRattachement() === Formation::RATTACHEMENT_PREVENTION) {
                    $this->getNotificationService()->triggerPrevention($inscription);
                }
            }
        }

        $vm =  new ViewModel([
            'title' => "Validation de l'inscription de ". $agent->getDenomination() ." à la formation ".$instance->getInstanceLibelle(). " du ".$instance->getPeriode(),
            'inscription' => $inscription,
            'form' => $form,
        ]);
        $vm->setTemplate('formation/formation-instance-inscrit/inscription');
        return $vm;
    }

    public function refuserDrhAction() : ViewModel
    {
        $inscription = $this->getFormationInstanceInscritService()->getRequestedFormationInstanceInscrit($this);
        $instance = $inscription->getInstance();
        $agent = $inscription->getAgent();

        $form = $this->getInscriptionForm();
        $form->setAttribute('action', $this->url()->fromRoute('formation-instance/refuser-drh', ['inscrit' => $inscription->getId(), 'agent' => $agent->getId()], [], true));
        $form->bind($inscription);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $justification = (isset($data['HasDescription']['description']) && trim($data['HasDescription']['description']) !== '')?trim($data['HasDescription']['description']):null;
                if ($justification === null) {
                    $this->flashMessenger()->addErrorMessage("<strong> Échec du refus </strong> <br/> Veuillez justifier votre refus !");
                } else {
                    $inscription->setJustificationRefus($justification);
                    $this->getEtatInstanceService()->setEtatActif($inscription,InscriptionEtats::ETAT_REFUSER);
                    $this->getFormationInstanceInscritService()->historise($inscription);
                    $this->flashMessenger()->addSuccessMessage("Refus effectué.");
                    $this->getNotificationService()->triggerDrhRefus($inscription);
                }
            }
        }

        $vm =  new ViewModel([
            'title' => "refus de l'inscription de ". $agent->getDenomination() ." à la formation ".$instance->getInstanceLibelle(). " du ".$instance->getPeriode(),
            'inscription' => $inscription,
            'form' => $form,
        ]);
        $vm->setTemplate('formation/formation-instance-inscrit/inscription');
        return $vm;
    }

    /** Classement ****************************************************************************************************/

    public function classerInscriptionAction() : Response
    {
        $inscription = $this->getFormationInstanceInscritService()->getRequestedFormationInstanceInscrit($this);

        $this->getFormationInstanceService()->classerInscription($inscription);

        switch ($inscription->getListe()) {
            case FormationInstanceInscrit::PRINCIPALE :
                $this->flashMessenger()->addSuccessMessage("Classement de l'inscription en liste principale.");
                break;
            case FormationInstanceInscrit::COMPLEMENTAIRE :
                $this->flashMessenger()->addWarningMessage("Liste principale complète. <br/> Classement de l'inscription en liste complémentaire.");
                break;
            default :
                $this->flashMessenger()->addErrorMessage("Plus de place en liste principale et complémentaire. <br/> Échec du classement de l'inscription.");
                break;
        }

        $retour = $this->params()->fromQuery('retour');
        if ($retour) return $this->redirect()->toUrl($retour);

        $session = $inscription->getInstance();
        return $this->redirect()->toRoute('formation-instance/afficher', ['formation-instance' => $session->getId()], ['fragment' => 'inscriptions'], true);
    }
}