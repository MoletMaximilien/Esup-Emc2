<?php

namespace Application\Service\Formation;

use Application\Entity\Db\FormationTheme;
use Application\Service\GestionEntiteHistorisationTrait;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use UnicaenApp\Exception\RuntimeException;
use UnicaenApp\Service\EntityManagerAwareTrait;
use UnicaenUtilisateur\Entity\DateTimeAwareTrait;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;

class FormationThemeService {
//    use EntityManagerAwareTrait;
//    use UserServiceAwareTrait;
//    use DateTimeAwareTrait;
    use GestionEntiteHistorisationTrait;

    /** GESTION DES ENTITES *******************************************************************************************/

    /**
     * @param FormationTheme $theme
     * @return FormationTheme
     */
    public function create(FormationTheme $theme)
    {
        $this->createFromTrait($theme);
        return $theme;
    }

    /**
     * @param FormationTheme $theme
     * @return FormationTheme
     */
    public function update(FormationTheme $theme)
    {
        $this->updateFromTrait($theme);
        return $theme;
    }

    /**
     * @param FormationTheme $theme
     * @return FormationTheme
     */
    public function historise(FormationTheme$theme)
    {
        $this->historiserFromTrait($theme);
        return $theme;
    }

    /**
     * @param FormationTheme $theme
     * @return FormationTheme
     */
    public function restore(FormationTheme $theme)
    {
        $this->restoreFromTrait($theme);
        return $theme;
    }

    /**
     * @param FormationTheme $theme
     * @return FormationTheme
     */
    public function delete(FormationTheme $theme)
    {
        $this->deleteFromTrait($theme);
        return $theme;
    }

    /** REQUETAGE *****************************************************************************************************/
    /**
     * @param bool $historiser
     * @param string $champ
     * @param string $order
     * @return FormationTheme[]
     */
    public function getFormationsThemes($historiser = false, $champ = 'libelle', $order = 'ASC')
    {
        $qb = $this->getEntityManager()->getRepository(FormationTheme::class)->createQueryBuilder('theme')
            ->addSelect('formation')->leftJoin('theme.formations', 'formation')
            ->orderBy('theme.'.$champ, $order);

        if ($historiser === false) {
            $qb = $qb->andWhere('theme.histoDestruction IS NULL');
        }

        $result = $qb->getQuery()->getResult();
        return $result;
    }
    /**
     * @param bool $historiser
     * @param string $champ
     * @param string $order
     * @return array
     */
    public function getFormationsThemesAsOptions($historiser = false, $champ = 'libelle', $order = 'ASC') {
        $themes = $this->getFormationsThemes($historiser, $champ, $order);

        $array = [];
        foreach ($themes as $theme) {
            $array[$theme->getId()] = $theme->getLibelle();
        }
        return $array;
    }

    /**
     * @param integer $id
     * @return FormationTheme
     */
    public function getFormationTheme($id)
    {
        $qb = $this->getEntityManager()->getRepository(FormationTheme::class)->createQueryBuilder('theme')
            ->andWhere('theme.id = :id')
            ->setParameter('id', $id)
        ;

        try {
            $result = $qb->getQuery()->getOneOrNullResult();
        } catch (ORMException $e) {
            throw new RuntimeException('Plusieurs FormationTheme partagent le même identifiant ['.$id.'].', $e);
        }
        return $result;
    }

    /**
     * @param AbstractActionController $controller
     * @param string $paramName
     * @return FormationTheme
     */
    public function getRequestedFormationTheme($controller, $paramName = "formation-theme")
    {
        $id = $controller->params()->fromRoute($paramName);
        $theme = $this->getFormationTheme($id);
        return $theme;
    }

}
