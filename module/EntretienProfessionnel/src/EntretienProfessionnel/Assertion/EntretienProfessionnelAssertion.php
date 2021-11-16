<?php

namespace EntretienProfessionnel\Assertion;

use Application\Constant\RoleConstant;
use Application\Service\Agent\AgentServiceAwareTrait;
use Application\Service\Structure\StructureServiceAwareTrait;
use EntretienProfessionnel\Entity\Db\EntretienProfessionnel;
use EntretienProfessionnel\Provider\Privilege\EntretienproPrivileges;
use UnicaenAuthentification\Assertion\AbstractAssertion;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;
use Zend\Permissions\Acl\Resource\ResourceInterface;

class EntretienProfessionnelAssertion extends AbstractAssertion {

    use AgentServiceAwareTrait;
    use UserServiceAwareTrait;
    use StructureServiceAwareTrait;

    protected function assertEntity(ResourceInterface $entity = null,  $privilege = null) : bool
    {
        if (!$entity instanceof EntretienProfessionnel) {
            return false;
        }


        /** @var EntretienProfessionnel $entity */
        $user = $this->getUserService()->getConnectedUser();
        $agent = $this->getAgentService()->getAgentByUser($user);
        $role = $this->getUserService()->getConnectedRole();

        $isResponsable = false;
        if ($role->getRoleId() === RoleConstant::RESPONSABLE) {
            $structures = [];
            foreach ($entity->getAgent()->getGrades() as $grade) {
                $structures[] = $grade->getStructure();
            }
            foreach ($entity->getAgent()->getStructuresForcees() as $structuresForcee) {
                $structures[] = $structuresForcee->getStructure();
            }
            foreach ($structures as $structure) {
                $isResponsable = $this->getStructureService()->isResponsable($structure, $agent);
                if ($isResponsable) break;
            }
        }

        $isHierarchie = false;
        $hierarchies = $this->getAgentService()->getResponsablesHierarchiques($entity->getAgent());
        foreach ($hierarchies as $hierarchie) {
            if ($hierarchie === $user) $isHierarchie = true;
        }

        switch($privilege) {
            case EntretienproPrivileges::ENTRETIENPRO_AFFICHER :
                switch ($role->getRoleId()) {
                    case RoleConstant::ADMIN_FONC:
                    case RoleConstant::ADMIN_TECH:
                    case RoleConstant::DRH:
                        return true;
                    case RoleConstant::PERSONNEL:
                        if ($entity->getAgent()->getUtilisateur() !== $user) return false;
                        // TODO DEBUT /!\ modification ad'hoc suite au demande du DRH /!\
                        if ($entity->getEtat()->getCode() === EntretienProfessionnel::ETAT_ACCEPTATION) return false;
                        if ($entity->getEtat()->getCode() === EntretienProfessionnel::ETAT_ACCEPTER) return false;
                        // TODO FIN
                        return true;
                    case RoleConstant::GESTIONNAIRE:
                        return  $entity->getResponsable() === $user;
                    case RoleConstant::RESPONSABLE:
                        return $isResponsable;
                    default:
                        return false;
                }
            case EntretienproPrivileges::ENTRETIENPRO_HISTORISER :
                switch ($role->getRoleId()) {
                    case RoleConstant::ADMIN_FONC:
                    case RoleConstant::ADMIN_TECH:
                    case RoleConstant::DRH:
                        return true;
                    case RoleConstant::GESTIONNAIRE:
                        return  $entity->getResponsable() === $user;
                    case RoleConstant::RESPONSABLE:
                        return $isResponsable;
                    default:
                        return false;
            }
            case EntretienproPrivileges::ENTRETIENPRO_VALIDER_RESPONSABLE :
                switch ($role->getRoleId()) {
                    case RoleConstant::ADMIN_FONC:
                    case RoleConstant::ADMIN_TECH:
                        return true;
                    default:
                        return $entity->getResponsable()->getUtilisateur() === $user;
                }
            case EntretienproPrivileges::ENTRETIENPRO_VALIDER_AGENT :
                switch ($role->getRoleId()) {
                    case RoleConstant::ADMIN_FONC:
                    case RoleConstant::ADMIN_TECH:
                        return true;
                    case RoleConstant::PERSONNEL:
                        return $entity->getAgent()->getUtilisateur() === $user;
                    default:
                        return false;
                }
            case EntretienproPrivileges::ENTRETIENPRO_VALIDER_DRH :
                switch ($role->getRoleId()) {
                    case RoleConstant::ADMIN_FONC:
                    case RoleConstant::ADMIN_TECH:
                    case RoleConstant::DRH:
                        return true;
                    case RoleConstant::RESPONSABLE:
                        return $isHierarchie;
                    default:
                        return false;
                }
        }
        return true;
    }
}