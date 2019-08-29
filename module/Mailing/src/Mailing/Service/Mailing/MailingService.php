<?php

namespace Mailing\Service\Mailing;

use Utilisateur\Entity\Db\Role;
use Utilisateur\Entity\Db\User;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Mailing\Model\Db\Mail;
use UnicaenApp\Exception\RuntimeException;
use UnicaenApp\Service\EntityManagerAwareTrait;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part;
use Zend\View\Renderer\PhpRenderer;

class MailingService {
    use EntityManagerAwareTrait;

    /** @var TransportInterface */
    private $transport;
    private $redirectTo;
    private $doNotSend;

    /** @var PhpRenderer */
    public $rendererService;

    public function __construct(TransportInterface $transport, $redirectTo, $doNotSend)
    {
        $this->transport    = $transport;
        $this->redirectTo   = $redirectTo;
        $this->doNotSend    = $doNotSend;
    }

    /**
     * @param User $utilisateur
     * @param Role $role
     * @param string $alteration (ajout, retrait)
     */
    public function notificationChangementRole($utilisateur, $role, $alteration)
    {
        $mail = $utilisateur->getEmail();
        $titre = $alteration . " du rôle ". $role->getRoleId();
        $texte  = "";
        switch($alteration) {
            case "ajout" : $texte .= "<p>Vous venez de recevoir le rôle de ".$role->getRoleId()." dans l'application PrEECoG.</p>";
                break;
            case "retrait" : $texte .= "<p>Vous venez de perdre le rôle de ".$role->getRoleId()." dans l'application PrEECoG.</p>";
                break;
        }
        $texte .= "<p>Pour vous connecter à celle-ci, suivez le lien suivant : <a href='".$this->rendererService->url('home', [], ['force_canonical' => true], true)."'>PrEECoG</a>.</p>";
        $this->sendMail($mail, $titre, $texte);
    }

//    public function notifierMedecinDirecteurDemande($demande) {
//        $denomination = $demande->getIdetudiant()->getPrenom() ." ". $demande->getIdetudiant()->getNom();
//        $composante = ($demande->getIdcomposante())?$demande->getIdcomposante()->getLibelle():"Non renseignée";
//        $date = ($demande->getDatecreation())?$demande->getDatecreation()->format("d/m/Y"):"Non renseignée";
//
//        $url = $this->rendererService->url('demande/detail', ['id' => $demande->getId()],  ['force_canonical' => true], true);
//
//        $sujet  = "";
//        if ($demande->getUrgence()) $sujet .= " *** URGENT ***";
//        if($demande->getVersion()>0) {
//            $sujet .= "Nouvel avenant à la demande d'aménagement à valider (id:" . $demande->getId() . ")";
//        } else {
//            $sujet .= "Nouvelle demande d'aménagement à valider (id:" . $demande->getId() . ")";
//        }
//
//        //TODO recuépérer cela de quelque part ...
//        $mail = "sumpps.handicap@unicaen.fr";
//
//        $texte  = "";
//        if ($demande->getUrgence()) $texte .= " *** URGENT ***";
//        $texte .= "<p> Bonjour, </p>";
//
//        if($demande->getVersion()>0) {
//            $texte .= "<p> L'avenant à la demande d'aménagement suivant attend votre validation : " . $url . "</p>";
//        } else {
//            $texte .= "<p> La demande d'aménagement suivante attend votre validation : " . $url . "</p>";
//        }
//        $texte .= "<table>";
//        $texte .= "<tr><th> Étudiant </th>     <td>". $denomination ."</td></tr>";
//        $texte .= "<tr><th> Composante </th>   <td>". $composante."</td></tr>";
//        $texte .= "<tr><th> En date du </th>   <td>". $date."</td></tr>";
//        $texte .= "</table>";
//
//
//        $this->sendMail($mail, $sujet, $texte);
//    }

    public function sendTestMail($adresse)
    {
        $this->sendMail($adresse, "Mail de test", "Ceci est un mail de test.");
    }

    /**
     * @param Mail $mail
     */
    public function reEnvoi($mail)
    {
        $this->sendMail(explode(",",$mail->getDestinatires()), $mail->getSujet(), $mail->getCorps());
    }

    public function sendMail($to, $subject, $texte, $attachement_path = null) {

        $message = (new Message())->setEncoding('UTF-8');
        $message->setFrom('ne-pas-repondre@unicaen.fr', "PrEECoG");
        if (!is_array($to)) $to = [ $to ];
        if ($this->doNotSend) {
            $message->addTo($this->redirectTo);
        } else {
            $message->addTo($to);
        }




        $mail = new Mail();
        $mail->setDateEnvoi(new DateTime());
        $mail->setStatusEnvoi(Mail::PENDING);
        $mail->setDestinatires(is_array($to)?implode(",",$to):$to);
        $mail->setRedir($this->doNotSend);
        $mail->setSujet($subject);
        $mail->setCorps($texte);
        $this->create($mail);


        $sujet = '[PrEECoG] ' . $subject;
        if ($this->doNotSend) {
            $sujet .= ' {REDIR}';
        }
        $message->setSubject($sujet);


        $texte = "<p><i>Ce courrier électronique vous a été adressé <strong>automatiquement</strong> par l'application PrEECoG. </i></p>" . $texte;

        if ($this->doNotSend) {
            $texte .= "<br/><br/><hr/><br/>";
            $texte .= "Initialement envoyé à :";
            $texte .= "<ul>";
            foreach ($to as $t) $texte .= "<li>".$t."</li>";
            $texte .= "</ul>";

        }

        $parts = [];

        $part = new Part($texte);
        $part->type = Mime::TYPE_HTML;
        $part->charset = 'UTF-8';
        $parts[] = $part;

        if ($attachement_path) {
            $attachement = new Part();
            $attachement->setType("application/pdf");
            $attachement->setContent(file_get_contents($attachement_path));
            $filename = explode('/', $attachement_path);
            $attachement->setFileName($filename[2]);
            $attachement->setEncoding(Mime::ENCODING_BASE64);
            $attachement->setDisposition(Mime::DISPOSITION_ATTACHMENT);
            $parts[] = $attachement;
        }

        $body = new MimeMessage();
        $body->setParts($parts);
        $message->setBody($body);

        $this->transport->send($message);

        $this->changerStatus($mail, Mail::SUCCESS);

    }

    /**
     * @return Mail[]
     */
    public function getMails()
    {
        $qb = $this->getEntityManager()->getRepository(Mail::class)->createQueryBuilder('mail')
            ->orderBy('mail.id', 'DESC')
        ;

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    /**
     * @param int $mailId
     * @return Mail
     */
    public function getMail($mailId)
    {
        $qb = $this->getEntityManager()->getRepository(Mail::class)->createQueryBuilder('mail')
            ->andWhere('mail.id = :id')
            ->setParameter('id', $mailId)
        ;

        try {
            $result = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new RuntimeException("Plusieurs mails ont le même identifiant");
        }

        return $result;
    }

    /**
     * @param Mail $mail
     * @return Mail
     */
    public function create($mail) {
        $this->getEntityManager()->persist($mail);
        try {
            $this->getEntityManager()->flush($mail);
        } catch (OptimisticLockException $e) {
            throw new RuntimeException("Problème lors de la création du mail", $e);
        }
        return $mail;
    }

    /**
     * @param Mail $mail
     * @return Mail
     */
    public function update($mail) {
        try {
            $this->getEntityManager()->flush($mail);
        } catch (OptimisticLockException $e) {
            throw new RuntimeException("Problème lors de la mise à jour du mail", $e);
        }
        return $mail;
    }

    /**
     * @param Mail $mail
     */
    public function delete($mail)
    {
        $this->getEntityManager()->remove($mail);
        try {
            $this->getEntityManager()->flush();
        } catch (OptimisticLockException $e) {
            throw new RuntimeException("Problème lors de la destruction du mail", $e);
        }
    }

    /**
     * @param Mail $mail
     * @param int $status
     * @return Mail
     */
    public function changerStatus($mail, $status)
    {
        $mail->setStatusEnvoi($status);
        $mail = $this->update($mail);
        return $mail;
    }
}