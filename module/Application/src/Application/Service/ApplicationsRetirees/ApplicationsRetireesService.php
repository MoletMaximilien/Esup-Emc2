<?php

namespace Application\Service\ApplicationsRetirees;

use Application\Entity\Db\Application;
use Application\Entity\Db\FichePoste;
use Application\Entity\Db\FicheposteApplicationRetiree;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Exception;
use UnicaenApp\Exception\RuntimeException;
use UnicaenApp\Service\EntityManagerAwareTrait;
use UnicaenUtilisateur\Entity\DateTimeAwareTrait;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

class ApplicationsRetireesService {
    use DateTimeAwareTrait;
    use EntityManagerAwareTrait;
    use UserServiceAwareTrait;

    /** GESTION DES ENTITES *******************************************************************************************/

    /**
     * @param FicheposteApplicationRetiree $applicationConservee
     * @return FicheposteApplicationRetiree
     */
    public function create(FicheposteApplicationRetiree $applicationConservee)
    {
        $date = $this->getDateTime();
        $user = $this->getUserService()->getConnectedUser();

        $applicationConservee->setHistoCreation($date);
        $applicationConservee->setHistoModification($date);
        $applicationConservee->setHistoCreateur($user);
        $applicationConservee->setHistoModificateur($user);

        try {
            $this->getEntityManager()->persist($applicationConservee);
            $this->getEntityManager()->flush($applicationConservee);
        } catch (ORMException $e) {
            throw new RuntimeException("Un problème est survenu lors de l'enregistrement en base.", 0 , $e);
        }

        return $applicationConservee;
    }

    /**
     * @param FicheposteApplicationRetiree $applicationConservee
     * @return FicheposteApplicationRetiree
     */
    public function update(FicheposteApplicationRetiree $applicationConservee)
    {
        $date = $this->getDateTime();
        $user = $this->getUserService()->getConnectedUser();

        $applicationConservee->setHistoModification($date);
        $applicationConservee->setHistoModificateur($user);

        try {
            $this->getEntityManager()->flush($applicationConservee);
        } catch (ORMException $e) {
            throw new RuntimeException("Un problème est survenu lors de l'enregistrement en base.", 0 , $e);
        }

        return $applicationConservee;
    }

    /**
     * @param FicheposteApplicationRetiree $applicationConservee
     * @return FicheposteApplicationRetiree
     */
    public function delete(FicheposteApplicationRetiree $applicationConservee)
    {
//        $date = $this->getDateTime();
//        $user = $this->getUserService()->getConnectedUser();
//
//        $applicationConservee->setHistoDestruction($date);
//        $applicationConservee->setHistoDestructeur($user);

        try {
            $this->getEntityManager()->remove($applicationConservee);
            $this->getEntityManager()->flush($applicationConservee);
        } catch (ORMException $e) {
            throw new RuntimeException("Un problème est survenu lors de l'enregistrement en base.", 0 , $e);
        }

        return $applicationConservee;
    }

    /** ACCESSEUR *****************************************************************************************************/

    /**
     * @param FichePoste $ficheposte
     * @param Application $application
     * @return FicheposteApplicationRetiree
     */
    public function getApplicationRetiree(FichePoste $ficheposte, Application $application)
    {
        $qb = $this->getEntityManager()->getRepository(FicheposteApplicationRetiree::class)->createQueryBuilder('retiree')
            ->andWhere('retiree.fichePoste = :ficheposte')
            ->andWhere('retiree.application = :application')
            ->setParameter('ficheposte', $ficheposte)
            ->setParameter('application', $application);

        try {
            $result = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new RuntimeException("Plusieurs ApplicationRetirees ...",0,$e);
        }
        return $result;

    }

    /**
     * @param FichePoste $ficheposte
     * @param Application $application
     * @return FicheposteApplicationRetiree
     */
    public function add(FichePoste $ficheposte, Application $application)
    {
        $result = $this->getApplicationRetiree($ficheposte, $application);

        if ($result === null) {
            $result = new FicheposteApplicationRetiree();
            $result->setFichePoste($ficheposte);
            $result->setApplication($application);
            $this->create($result);
        }
        return $result;
    }

    /**
     * @param FichePoste $ficheposte
     * @param Application $application
     * @return FicheposteApplicationRetiree
     */
    public function remove(FichePoste $ficheposte, Application $application)
    {
        $result = $this->getApplicationRetiree($ficheposte, $application);

        if ($result !== null) {
            $this->delete($result);
        }
        return $result;
    }

}