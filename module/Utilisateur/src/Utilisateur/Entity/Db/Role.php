<?php

namespace Utilisateur\Entity\Db;
use Doctrine\Common\Collections\ArrayCollection;
use UnicaenAuth\Entity\Db\AbstractRole;
use UnicaenAuth\Entity\Db\UserInterface;

/**
 * Role
 */
class Role extends AbstractRole
{
    //TODO remove it form there
    const GESTIONNAIRE = 'Gestionnaire de structure';
    const ADMIN_TECH = 'Administrateur technique';
    const ADMIN_FONC = 'Administrateur fonctionnel';
    const UTILISATEUR = 'Utilisateur';
    const PERSONNEL = 'Personnel';


    /** @var ArrayCollection */
    private $privilege;

    public function __construct()
    {
        parent::__construct();
        $this->privilege = new ArrayCollection();
    }

    /**
     * @param UserInterface $user
     * @return Role
     */
    public function addUser(UserInterface $user)
    {
        $this->users[] = $user;
        return $this;
    }

    /**
     * @param UserInterface $user
     * @return Role
     */
    public function removeUser(UserInterface $user)
    {
        $this->users->removeElement($user);
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param Privilege $privilege
     * @return Role
     */
    public function addPrivilege(Privilege $privilege)
    {
        $this->privilege[] = $privilege;
        return $this;
    }

    /**
     * @param Privilege $privilege
     */
    public function removePrivilege(Privilege $privilege)
    {
        $this->privilege->removeElement($privilege);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrivileges()
    {
        return $this->privilege;
    }
}
