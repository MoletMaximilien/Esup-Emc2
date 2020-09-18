<?php

namespace UnicaenDocument\Service\Contenu;

use Application\Service\GestionEntiteHistorisationTrait;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use UnicaenApp\Exception\RuntimeException;
use UnicaenDocument\Entity\Db\Content;
use UnicaenDocument\Service\Macro\MacroServiceAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;

class ContenuService {
    use MacroServiceAwareTrait;
    use GestionEntiteHistorisationTrait;

    /** GESTION DES ENTITES *******************************************************************************************/

    /**
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        $this->createFromTrait($content);
        return $content;
    }

    /**
     * @param Content $content
     * @return Content
     */
    public function update(Content $content)
    {
        $this->updateFromTrait($content);
        return $content;
    }

    /**
     * @param Content $content
     * @return Content
     */
    public function historise(Content $content)
    {
        $this->historiserFromTrait($content);
        return $content;
    }

    /**
     * @param Content $content
     * @return Content
     */
    public function restore(Content $content)
    {
        $this->restoreFromTrait($content);
        return $content;
    }

    /**
     * @param Content $content
     * @return Content
     */
    public function delete(Content $content)
    {
        $this->deleteFromTrait($content);
        return $content;
    }

    /** REQUETAGE *****************************************************************************************************/

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        $qb = $this->getEntityManager()->getRepository(Content::class)->createQueryBuilder('contenu')
        ;

        return $qb;
    }

    /**
     * @param string $champ
     * @param string $ordre
     * @return Content[]
     */
    public function getContenus($champ = 'code', $ordre = 'ASC')
    {
        $qb = $this->createQueryBuilder()
            ->orderBy('contenu.' . $champ, $ordre)
        ;

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    /**
     * @param integer $id
     * @return Content
     */
    public function getContenu(int $id)
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('contenu.id = :id')
            ->setParameter('id', $id)
        ;

        try {
            $result = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new RuntimeException("Plusieurs Content partagent le même id [".$id."]");
        }
        return $result;
    }

    /**
     * @param string $code
     * @return Content
     */
    public function getContenuByCode(string $code)
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('contenu.code = :code')
            ->setParameter('code', $code)
        ;

        try {
            $result = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new RuntimeException("Plusieurs Content partagent le même code [".$code."]");
        }
        return $result;
    }

    /**
     * @param AbstractActionController $controller
     * @param string $param
     * @return Content
     */
    public function getRequestedContenu(AbstractActionController $controller, string $param='contenu')
    {
        $id = $controller->params()->fromRoute($param);
        $result = $this->getContenu($id);

        return $result;
    }

    /** TRAITEMENTS DES MACROS ****************************************************************************************/


}