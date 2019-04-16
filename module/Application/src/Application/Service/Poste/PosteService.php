<?php

namespace Application\Service\Poste;

use Application\Entity\Db\Poste;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use UnicaenApp\Exception\RuntimeException;
use UnicaenApp\Service\EntityManagerAwareTrait;

class PosteService {
    use EntityManagerAwareTrait;

    /**
     * @param string $order
     * @return Poste[]
     */
    public function getPostes($order = null) {
        $qb = $this->getEntityManager()->getRepository(Poste::class)->createQueryBuilder('poste')
            ;

        if ($order) {
            $qb = $qb->orderBy('poste.'.$order, 'ASC');

        } else {
            $qb = $qb->orderBy('poste.numeroPoste', 'ASC');
        }

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    /**
     * @param integer $id
     * @return Poste
     */
    public function getPoste($id) {
        $qb = $this->getEntityManager()->getRepository(Poste::class)->createQueryBuilder('poste')
            ->andWhere('poste.id = :id')
            ->setParameter('id', $id)
        ;

        try {
            $result = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new RuntimeException("Plusieurs postes partagent le même identifiant [".$id."].",$e);
        }
        return $result;
    }

    /**
     * @param Poste $poste
     * @return Poste
     */
    public function create($poste) {
        $this->getEntityManager()->persist($poste);
        try {
            $this->getEntityManager()->flush($poste);
        } catch (OptimisticLockException $e) {
            throw new RuntimeException("Problème lors de la création en base du Poste.",$e);
        }
        return $poste;
    }

    /**
     * @param Poste $poste
     * @return Poste
     */
    public function update($poste) {
        try {
            $this->getEntityManager()->flush($poste);
        } catch (OptimisticLockException $e) {
            throw new RuntimeException("Problème lors de la mise à jour en base du Poste.",$e);
        }
        return $poste;
    }

    public function delete($poste) {
        $this->getEntityManager()->remove($poste);
        try {
            $this->getEntityManager()->flush();
        } catch (OptimisticLockException $e) {
            throw new RuntimeException("Problème lors de la suppression en base du Poste.",$e);
        }
    }

}