<?php

namespace Application\Service\AgentAffectation;

use Application\Entity\Db\Agent;
use Application\Entity\Db\AgentAffectation;
use Application\Entity\Db\Structure;
use Doctrine\ORM\QueryBuilder;
use UnicaenApp\Service\EntityManagerAwareTrait;

class AgentAffectationService {
    use EntityManagerAwareTrait;

    /** REQUETAGE *****************************************************************************************************/

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder() : QueryBuilder
    {
        $qb = $this->getEntityManager()->getRepository(AgentAffectation::class)->createQueryBuilder('agentaffectation')
            ->join('agentaffectation.agent', 'agent')->addSelect('agent')
            ->join('agentaffectation.structure', 'structure')->addSelect('structure')
            ->andWhere('agentaffectation.deleted_on IS NULL')
        ;
        return $qb;
    }

    /**
     * @param Agent $agent
     * @param bool $actif
     * @return array
     */
    public function getAgentAffectationsByAgent(Agent $agent, bool $actif = true) : array
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('agentaffectation.agent = :agent')
            ->setParameter('agent', $agent)
        ;

        if ($actif === true) $qb = AgentAffectation::decorateWithActif($qb, 'agentaffectation');

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    /**
     * @param Structure $structure
     * @param bool $actif
     * @return array
     */
    public function getAgentAffectationsByStructure(Structure $structure, bool $actif = true) : array
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('agentaffectation.structure = :structure')
            ->setParameter('structure', $structure)
        ;

        if ($actif === true) $qb = AgentAffectation::decorateWithActif($qb, 'agentaffectation');

        $result = $qb->getQuery()->getResult();
        return $result;
    }
}