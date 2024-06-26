<?php

namespace Application\Entity\Db;

use Doctrine\ORM\QueryBuilder;
use UnicaenUtilisateur\Entity\Db\HistoriqueAwareInterface;
use UnicaenUtilisateur\Entity\Db\HistoriqueAwareTrait;

class AgentAutorite implements HistoriqueAwareInterface
{
    use HistoriqueAwareTrait;

    private ?int $id = null;
    private ?Agent $agent = null;
    private ?Agent $autorite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): void
    {
        $this->agent = $agent;
    }

    public function getAutorite(): ?Agent
    {
        return $this->autorite;
    }

    public function setAutorite(?Agent $autorite): void
    {
        $this->autorite = $autorite;
    }

    /** decorators ************************************/

    static public function decorateWithAgentAutorite(QueryBuilder $qb, string $entityname = 'agent', bool $histo = false) : QueryBuilder
    {
        $qb = $qb
            //Chaine hierarchique
            ->leftJoin($entityname . '.autorites', 'autorite')
//            ->addSelect('autorite')
            ->leftJoin('autorite.autorite', 'aautorite')
//            ->addSelect('aautorite')
        ;
        if ($histo === false) {
            $qb = $qb->andWhere('autorite.histoDestruction IS NULL');
        }

        return $qb;
    }

}