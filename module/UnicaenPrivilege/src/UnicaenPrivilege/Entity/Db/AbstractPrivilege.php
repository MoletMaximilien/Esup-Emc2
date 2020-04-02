<?php

namespace UnicaenPrivilege\Entity\Db;

use Doctrine\Common\Collections\Collection;
use Zend\Permissions\Acl\Resource\ResourceInterface;


/**
 * Privilege entity abstract mother class.
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractPrivilege implements PrivilegeInterface, ResourceInterface
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=150, unique=false, nullable=false)
     */
    protected $code;

    /**
     * @var string
     * @ORM\Column(name="libelle", type="string", length=200, unique=false, nullable=false)
     */
    protected $libelle;

    /**
     * @var int
     * @ORM\Column(name="ordre", type="integer", unique=false, nullable=true)
     */
    protected $ordre;

    /**
     * @var CategoriePrivilege
     * @ORM\ManyToOne(targetEntity="CategoriePrivilege", inversedBy="privilege")
     * @ORM\JoinColumn(name="categorie_id", referencedColumnName="id")
     */
    protected $categorie;

    /**
     * @ORM\ManyToMany(targetEntity="UnicaenAuth\Entity\Db\Role",cascade={"all"})
     * @ORM\JoinTable(
     *     name="role_privilege",
     *     joinColumns={@ORM\JoinColumn(name="privilege_id", referencedColumnName="id", onDelete="cascade")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="cascade")}
     *
     * )
     */
    protected $role;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->role = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Privilege
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    public function getFullCode()
    {
        return $this->getCategorie()->getCode() . '-' . $this->getCode();
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Privilege
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     *
     * @return integer
     */
    function getOrdre()
    {
        return $this->ordre;
    }

    /**
     *
     * @param integer $ordre
     *
     * @return self
     */
    function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set categorie
     *
     * @param CategoriePrivilege $categorie
     *
     * @return self
     */
    public function setCategorie(CategoriePrivilege $categorie = null)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return CategoriePrivilege
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Add role
     *
     * @param RoleInterface $role
     *
     * @return self
     */
    public function addRole(RoleInterface $role)
    {
        $this->role->add($role);

        return $this;
    }

    /**
     * Remove role
     *
     * @param RoleInterface $role
     */
    public function removeRole(RoleInterface $role)
    {
        $this->role->removeElement($role);
    }

    /**
     * Get role
     *
     * @return Collection
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getLibelle();
    }

    /**
     * @return string
     */
    public function getResourceId()
    {
        return Privileges::getResourceId($this);
    }
}