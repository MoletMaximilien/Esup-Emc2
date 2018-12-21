<?php

namespace Application\Entity\Db;

class Fonction {

    /** @var integer */
    private $id;
    /** @var string */
    private $libelle;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param string $libelle
     * @return Fonction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
        return $this;
    }


};