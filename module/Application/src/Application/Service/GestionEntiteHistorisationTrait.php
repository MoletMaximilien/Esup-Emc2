<?php

namespace Application\Service;

use DateTime;
use Doctrine\ORM\ORMException;
use UnicaenApp\Entity\HistoriqueAwareInterface;
use UnicaenApp\Exception\RuntimeException;
use UnicaenApp\Service\EntityManagerAwareTrait;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

trait GestionEntiteHistorisationTrait {
    use EntityManagerAwareTrait;
    use UserServiceAwareTrait;

    /**
     * @param HistoriqueAwareInterface $object
     * @return HistoriqueAwareInterface
     */
    public function createFromTrait(HistoriqueAwareInterface $object)
    {
        $user = $this->getUserService()->getConnectedUser();
        if ($user === null) $user = $this->getUserService()->getUtilisateur(0);
        $date =new DateTime();

        $object->setHistoCreation($date);
        $object->setHistoCreateur($user);
        $object->setHistoModification($date);
        $object->setHistoModificateur($user);

        try {
            $this->getEntityManager()->persist($object);
            $this->getEntityManager()->flush($object);
        } catch (ORMException $e) {
            throw new RuntimeException("Un problème est survenue lors de l'enregistrement en BD.", $e);
        }

        return $object;
    }

    /**
     * @param HistoriqueAwareInterface $object
     * @return HistoriqueAwareInterface
     */
    public function updateFromTrait(HistoriqueAwareInterface $object)
    {
        $user = $this->getUserService()->getConnectedUser();
        if ($user === null) $user = $this->getUserService()->getUtilisateur(0);
        $date = (new DateTime());

        $object->setHistoModification($date);
        $object->setHistoModificateur($user);

        try {
            $this->getEntityManager()->flush($object);
        } catch (ORMException $e) {
            throw new RuntimeException("Un problème est survenue lors de l'enregistrement en BD.", $e);
        }

        return $object;
    }

    /**
     * @param HistoriqueAwareInterface $object
     * @return HistoriqueAwareInterface
     */
    public function historiserFromTrait(HistoriqueAwareInterface $object)
    {
        $user = $this->getUserService()->getConnectedUser();
        if ($user === null) $user = $this->getUserService()->getUtilisateur(0);
        $date = (new DateTime());

        $object->setHistoDestruction($date);
        $object->setHistoDestructeur($user);

        try {
            $this->getEntityManager()->flush($object);
        } catch (ORMException $e) {
            throw new RuntimeException("Un problème est survenue lors de l'enregistrement en BD.", $e);
        }

        return $object;
    }

    /**
     * @param HistoriqueAwareInterface $object
     * @return HistoriqueAwareInterface
     */
    public function restoreFromTrait(HistoriqueAwareInterface $object)
    {
        $object->setHistoDestruction(null);
        $object->setHistoDestructeur(null);

        try {
            $this->getEntityManager()->flush($object);
        } catch (ORMException $e) {
            throw new RuntimeException("Un problème est survenue lors de l'enregistrement en BD.", $e);
        }

        return $object;
    }

    /**
     * @param HistoriqueAwareInterface $object
     * @return HistoriqueAwareInterface
     */
    public function deleteFromTrait(HistoriqueAwareInterface $object)
    {
        try {
            $this->getEntityManager()->remove($object);
            $this->getEntityManager()->flush($object);
        } catch (ORMException $e) {
            throw new RuntimeException("Un problème est survenue lors de l'enregistrement en BD.", $e);
        }

        return $object;
    }


}