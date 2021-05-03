<?php

namespace Formation\Controller;

use DateInterval;
use DateTime;
use Exception;
use Formation\Entity\Db\FormationInstance;
use Formation\Service\FormationInstance\FormationInstanceServiceAwareTrait;
use Mailing\Service\Mailing\MailingServiceAwareTrait;
use UnicaenEtat\Service\Etat\EtatServiceAwareTrait;
use UnicaenParametre\Service\Parametre\ParametreServiceAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Controleur pour gérer les routes 'console' du module de formation
 *
 */
class FormationConsoleController extends AbstractActionController {
    use EtatServiceAwareTrait;
    use FormationInstanceServiceAwareTrait;
    use MailingServiceAwareTrait;
    use ParametreServiceAwareTrait;

    /** Notifie les convocations et passe la session a débuter
     * @throws Exception
     */
    public function notifierConvocationAction()
    {
        echo (new DateTime())->format('d/m/y à H:i:s') . "\n";
        echo "Notifier Convocation" . "\n";

        $delai = $this->getParametreService()->getParametreByCode('FORMATION', 'INTERVAL_CONVOCATION');
        if ($delai === null) throw new Exception("Le parametre FORMATION\INTERVAL_CONVOCATION n'est pas déclaré ! ");
        if ($delai->getValeur() == "")       throw new Exception("Le parametre FORMATION\INTERVAL_CONVOCATION n'a pas de valeur ! ");

        $now = new DateTime();
        $now->add(new DateInterval('P'.$delai->getValeur().'D'));
        $sessions = $this->getFormationInstanceService()->getFormationsInstancesByEtat(FormationInstance::ETAT_INSCRIPTION_FERMEE);
        foreach ($sessions as $session) {
            $debut = DateTime::createFromFormat('d/m/Y',$session->getDebut());
            if ($debut <= $now) {
                $nb = 0;
                foreach ($session->getListePrincipale() as $inscrit) {
                    $mail = $this->getMailingService()->sendMailType('FORMATION_CONVOCATION', ['formation' => $session->getFormation(), 'instance' => $session, 'agent' => $inscrit->getAgent(), 'mailing' => $inscrit->getAgent()->getEmail()]);
                    $mail->setAttachementType(FormationInstance::class);
                    $mail->setAttachementId($session->getId());
                    $this->getMailingService()->update($mail);
                    $nb++;
                }
                $session->setEtat($this->getEtatService()->getEtatByCode(FormationInstance::ETAT_FORMATION_CONVOCATION));
                $this->getFormationInstanceService()->update($session);
                echo (new DateTime())->format('d/m/y à H:i:s') . "\n";
                echo "Envoi des convocations effectué pour la session de formation " . $session->getFormation()->getLibelle() . " - " . $session->getId() . "\n";
                echo $nb . " convocations envoyées.\n";
            }

        }
    }
}