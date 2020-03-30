<?php

namespace Application\Service\Formation;

use Application\Entity\Db\Formation;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use UnicaenApp\Exception\RuntimeException;
use UnicaenApp\Service\EntityManagerAwareTrait;
use UnicaenUtilisateur\Entity\DateTimeAwareTrait;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;

class FormationService {
    use EntityManagerAwareTrait;
    use FormationThemeServiceAwareTrait;
    use UserServiceAwareTrait;
    use DateTimeAwareTrait;

    /** GESTION DES ENTITES *******************************************************************************************/

    /**
     * @param Formation $formation
     * @return Formation
     */
    public function create(Formation $formation)
    {
        $date = $this->getDateTime();
        $user = $this->getUserService()->getConnectedUser();

        $formation->setHistoCreation($date);
        $formation->setHistoCreateur($user);
        $formation->setHistoModification($date);
        $formation->setHistoModificateur($user);

        try {
            $this->getEntityManager()->persist($formation);
            $this->getEntityManager()->flush($formation);
        } catch (ORMException $e) {
            throw new RuntimeException('Un problème est survenu lors de la création en BD', $e);
        }
        return $formation;
    }

    /**
     * @param Formation $formation
     * @return Formation
     */
    public function update(Formation $formation)
    {
        $date = $this->getDateTime();
        $user = $this->getUserService()->getConnectedUser();

        $formation->setHistoModification($date);
        $formation->setHistoModificateur($user);

        try {
            $this->getEntityManager()->flush($formation);
        } catch (ORMException $e) {
            throw new RuntimeException('Un problème est survenu lors de la mise à jour en BD', $e);
        }
        return $formation;
    }

    /**
     * @param Formation $formation
     * @return Formation
     */
    public function historise(Formation $formation)
    {
        $date = $this->getDateTime();
        $user = $this->getUserService()->getConnectedUser();

        $formation->setHistoDestruction($date);
        $formation->setHistoDestructeur($user);

        try {
            $this->getEntityManager()->flush($formation);
        } catch (ORMException $e) {
            throw new RuntimeException('Un problème est survenu lors de la mise à jour en BD', $e);
        }
        return $formation;
    }

    /**
     * @param Formation $formation
     * @return Formation
     */
    public function restore(Formation $formation)
    {
        $formation->setHistoDestruction(null);
        $formation->setHistoDestructeur(null);

        try {
            $this->getEntityManager()->flush($formation);
        } catch (ORMException $e) {
            throw new RuntimeException('Un problème est survenu lors de la mise à jour en BD', $e);
        }
        return $formation;
    }

    /**
     * @param Formation $formation
     * @return Formation
     */
    public function delete(Formation $formation)
    {
        try {
            $this->getEntityManager()->remove($formation);
            $this->getEntityManager()->flush();
        } catch (ORMException $e) {
            throw new RuntimeException('Un problème est survenu lors de la suppression en BD', $e);
        }
        return $formation;
    }

    /** REQUETAGES ****************************************************************************************************/

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        $qb = $this->getEntityManager()->getRepository(Formation::class)->createQueryBuilder('formation')
            ->addSelect('modificateur')->leftJoin('formation.histoModificateur', 'modificateur')
            ->addSelect('theme')->leftJoin('formation.theme', 'theme')
            ->andWhere('formation.histoDestruction IS NULL');
        return $qb;
    }

    /**
     * @param string $champ
     * @param string $ordre
     * @return Formation[]
     */
    public function getFormations($champ = 'libelle', $ordre = 'ASC')
    {
        $qb = $this->createQueryBuilder()
            ->orderBy('formation.' . $champ, $ordre)
        ;
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    /**
     * @param string $champ
     * @param string $order
     * @return Formation[]
     */
    private function getFormationsSansThemes($champ = 'libelle', $order = 'ASC')
    {
        $qb = $this->createQueryBuilder()
            ->orderBy('formation.'.$champ, $order)
            ->andWhere('formation.theme IS NULL')
        ;
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    /**
     * @param int $id
     * @return Formation
     */
    public function getFormation($id)
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('formation.id = :id')
            ->setParameter('id', $id)
        ;

        try {
            $result = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new RuntimeException('Plusieurs formations portent le même identifiant ['.$id.']', $e);
        }
        return $result;
    }

    /**
     * @param AbstractActionController $controller
     * @param string $paramName
     * @return Formation
     */
    public function getRequestedFormation($controller, $paramName = 'formation')
    {
        $id = $controller->params()->fromRoute($paramName);
        $activite = $this->getFormation($id);
        return $activite;
    }

    /**
     * @return array
     */
    public function getFormationsAsOptions() {
        $formations = $this->getFormations('libelle');

        $result = [];
        foreach ($formations as $formation) {
            $result[$formation->getId()] = $formation->getLibelle();
        }
        return $result;
    }

    /**
     * @param Formation[] $formationsAlreadyUsed
     * @return Formation[]
     */
    public function getFormationsDisponiblesAsOptions($formationsAlreadyUsed)
    {
        $formations = $this->getFormations('libelle', 'ASC');

        $result = [];
        foreach ($formations as $formation) {
            $found = false;
            if ($formationsAlreadyUsed !== null) {
                foreach ($formationsAlreadyUsed as $used) {
                    if ($used->getId() === $formation->getId()) {
                        $found = true;
                        break;
                    }
                }
            }
            if (! $found ) $result[] = $formation;
        }

        return Formation::generateOptions($result);
    }

    /** FORMATION THEME ***********************************************************************************************/

    /**
     * @return array
     */
    public function getFormationsThemesAsGroupOptions()
    {
        $themes = $this->getFormationThemeService()->getFormationsThemes();
        $sanstheme = $this->getFormationsSansThemes();
        $options = [];

        foreach ($themes as $theme) {
            $optionsoptions = [];
            foreach ($theme->getFormations() as $formation) {
                $optionsoptions[$formation->getId()] = $formation->getLibelle();
            }
            asort($optionsoptions);
            $array = [
                'label' => $theme->getLibelle(),
                'options' => $optionsoptions,
            ];
            $options[] = $array;
        }

        if (!empty($sanstheme)) {
            $optionsoptions = [];
            foreach ($sanstheme as $formation) {
                $optionsoptions[$formation->getId()] = $formation->getLibelle();
            }
            asort($optionsoptions);
            $array = [
                'label' => "Sans thème",
                'options' => $optionsoptions,
            ];
            $options[] = $array;
        }

        return $options;
    }
}
