<?php

namespace Formation\Service\Inscription;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Formation\Entity\Db\Inscription;
use Laminas\Mvc\Controller\AbstractActionController;
use RuntimeException;

class InscriptionService {
    use ProvidesObjectManager;

    /** Gestion des  entités ******************************************************************************************/

    public function create(Inscription $inscription): Inscription
    {
        $this->getObjectManager()->persist($inscription);
        $this->getObjectManager()->flush($inscription);
        return $inscription;
    }

    public function update(Inscription $inscription): Inscription
    {
        $this->getObjectManager()->flush($inscription);
        return $inscription;
    }

    public function historise(Inscription $inscription): Inscription
    {
        $inscription->historiser();
        $this->getObjectManager()->flush($inscription);
        return $inscription;
    }

    public function restore(Inscription $inscription): Inscription
    {
        $inscription->dehistoriser();
        $this->getObjectManager()->flush($inscription);
        return $inscription;
    }

    public function delete(Inscription $inscription): Inscription
    {
        $this->getObjectManager()->remove($inscription);
        $this->getObjectManager()->flush($inscription);
        return $inscription;
    }

    /** REQUETAGE *****************************************************************************************************/

    public function createQueryBuilder(): QueryBuilder
    {
        $qb = $this->getObjectManager()->getRepository(Inscription::class)->createQueryBuilder('inscription')
            ->leftJoin('inscription.session', 'session')->addSelect('session')
            ->leftJoin('inscription.agent', 'agent')->addSelect('agent')
            ->leftJoin('inscription.stagiaire', 'stagiaire')->addSelect('stagiaire')
        ;
        return $qb;
    }

    /** @return Inscription[] */
    public function getInscriptions(string $champ='histoCreation', string $ordre='DESC', bool $withHisto=false): array
    {
        $qb= $this->createQueryBuilder()
            ->orderBy('inscription.'.$champ, $ordre);
        if (!$withHisto) $qb = $qb->andWhere('inscription.histoDestruction IS NULL');

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function getInscription(?int $id): ?Inscription
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('inscription.id = :id')->setParameter('id', $id);

        try {
            $result = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new RuntimeException("Plusieurs [".Inscription::class."] partagent le même id [".$id."]",0,$e);
        }
        return $result;
    }

    public function getRequestedInscription(AbstractActionController $controller, string $param = 'inscription'): ?Inscription
    {
        $id = $controller->params()->fromRoute($param);
        $result = $this->getInscription($id);
        return $result;
    }

    /** FACADE ********************************************************************************************************/



}