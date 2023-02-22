<?php

namespace Application\Service\FicheMetier;

use Application\Controller\FicheMetierController;
use Application\Entity\Db\Activite;
use Application\Entity\Db\ActiviteDescription;
use Application\Provider\Template\PdfTemplate;
use Application\Service\Configuration\ConfigurationServiceAwareTrait;
use FicheMetier\Entity\Db\FicheMetier;
use Application\Entity\Db\FicheMetierActivite;
use Application\Provider\Etat\FicheMetierEtats;
use Application\Service\Activite\ActiviteServiceAwareTrait;
use Application\Service\ActiviteDescription\ActiviteDescriptionServiceAwareTrait;
use Carriere\Service\Niveau\NiveauService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Element\Entity\Db\ApplicationElement;
use Element\Entity\Db\Competence;
use Element\Entity\Db\CompetenceElement;
use Element\Service\Application\ApplicationServiceAwareTrait;
use Element\Service\ApplicationElement\ApplicationElementServiceAwareTrait;
use Element\Service\Competence\CompetenceServiceAwareTrait;
use Element\Service\CompetenceElement\CompetenceElementServiceAwareTrait;
use Element\Service\HasApplicationCollection\HasApplicationCollectionServiceAwareTrait;
use Element\Service\HasCompetenceCollection\HasCompetenceCollectionServiceAwareTrait;
use Laminas\Form\Form;
use Laminas\Http\Request;
use Laminas\Mvc\Controller\AbstractController;
use Metier\Entity\Db\Domaine;
use Metier\Service\Domaine\DomaineServiceAwareTrait;
use Metier\Service\Metier\MetierServiceAwareTrait;
use Mpdf\MpdfException;
use UnicaenApp\Exception\RuntimeException;
use UnicaenApp\Service\EntityManagerAwareTrait;
use UnicaenEtat\Service\Etat\EtatServiceAwareTrait;
use UnicaenPdf\Exporter\PdfExporter;
use UnicaenRenderer\Service\Rendu\RenduServiceAwareTrait;

class FicheMetierService {
    use ApplicationServiceAwareTrait;
    use ApplicationElementServiceAwareTrait;
    use CompetenceServiceAwareTrait;
    use CompetenceElementServiceAwareTrait;
    use ConfigurationServiceAwareTrait;
    use DomaineServiceAwareTrait;
    use EtatServiceAwareTrait;
    use EntityManagerAwareTrait;
    use RenduServiceAwareTrait;

    use ActiviteServiceAwareTrait;
    use ActiviteDescriptionServiceAwareTrait;
    use HasApplicationCollectionServiceAwareTrait;
    use HasCompetenceCollectionServiceAwareTrait;
    use MetierServiceAwareTrait;

    /** GESTION DES ENTITES *******************************************************************************************/

    /**
     * @param FicheMetier $fiche
     * @return FicheMetier
     */
    public function create(FicheMetier $fiche) : FicheMetier
    {
        try {
            $this->getEntityManager()->persist($fiche);
            $this->getEntityManager()->flush($fiche);
        } catch (ORMException $e) {
            throw new RuntimeException("Un problème est survenue lors de l'enregistrement en BD.", $e);
        }
        return $fiche;
    }

    /**
     * @param FicheMetier $fiche
     * @return FicheMetier
     */
    public function update(FicheMetier $fiche) : FicheMetier
    {
        try {
            $this->getEntityManager()->flush($fiche);
        } catch (ORMException $e) {
            throw new RuntimeException("Un problème est survenue lors de l'enregistrement en BD.", $e);
        }
        return $fiche;
    }

    /**
     * @param FicheMetier $fiche
     * @return FicheMetier
     */
    public function historise(FicheMetier $fiche) : FicheMetier
    {
        try {
            $fiche->historiser();
            $this->getEntityManager()->flush($fiche);
        } catch (ORMException $e) {
            throw new RuntimeException("Un problème est survenue lors de l'enregistrement en BD.", $e);
        }
        return $fiche;
    }

    /**
     * @param FicheMetier $fiche
     * @return FicheMetier
     */
    public function restore(FicheMetier $fiche) : FicheMetier
    {
        try {
            $fiche->dehistoriser();
            $this->getEntityManager()->flush($fiche);
        } catch (ORMException $e) {
            throw new RuntimeException("Un problème est survenue lors de l'enregistrement en BD.", $e);
        }
        return $fiche;
    }

    /**
     * @param FicheMetier $fiche
     * @return FicheMetier
     */
    public function delete(FicheMetier $fiche) : FicheMetier
    {
        try {
            $this->getEntityManager()->remove($fiche);
            $this->getEntityManager()->flush($fiche);
        } catch (ORMException $e) {
            throw new RuntimeException("Un problème est survenue lors de l'enregistrement en BD.", $e);
        }
        return $fiche;
    }

    /** REQUETAGE *****************************************************************************************************/

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder() : QueryBuilder
    {
        $qb = $this->getEntityManager()->getRepository(FicheMetier::class)->createQueryBuilder('ficheMetier')
            ->addSelect('metier')->join('ficheMetier.metier', 'metier')
            ->addSelect('domaine')->join('metier.domaines', 'domaine')
            ->addSelect('famille')->join('domaine.familles', 'famille')
            ->addSelect('etat')->join('ficheMetier.etat', 'etat')
            ->addSelect('etype')->join('etat.type', 'etype')
            ->addSelect('reference')->leftJoin('metier.references', 'reference')
            ->addSelect('referentiel')->leftJoin('reference.referentiel', 'referentiel')
            ;
        $qb = NiveauService::decorateWithNiveau($qb,'metier');
        return $qb;
    }

    /**
     * @param string $order an attribute use to sort
     * @return FicheMetier[]
     */
    public function getFichesMetiers(string $order = 'id') : array
    {
       $qb = $this->createQueryBuilder()
//            ->addSelect('application')->leftJoin('ficheMetier.applications', 'application')
//            ->addSelect('formation')->leftJoin('ficheMetier.formations', 'formation')
//            ->addSelect('competence')->leftJoin('ficheMetier.competences', 'competence')
            ->orderBy('ficheMetier.', $order)
        ;

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    /**
     * @param array $filtre
     * @param string $champ
     * @param string $ordre
     * @return FicheMetier[]
     */
    public function getFichesMetiersWithFiltre(array $filtre, string $champ = 'id', string $ordre = 'DESC') : array
    {
        $qb = $this->createQueryBuilder()
            ->orderBy('ficheMetier.' . $champ, $ordre)
        ;

        if (isset($filtre['expertise']) AND $filtre['expertise'] != '') {
            $expertise = null;
            if ($filtre['expertise'] == "1") $expertise = true;
            if ($filtre['expertise'] == "0") $expertise = false;
            if ($expertise !== null) $qb = $qb->andWhere('ficheMetier.hasExpertise = :expertise')->setParameter('expertise', $expertise);
        }
        if (isset($filtre['etat']) AND $filtre['etat'] != '') {
            $qb = $qb->andWhere('etat.id = :etat')->setParameter('etat', $filtre['etat']);
        }
        if (isset($filtre['domaine']) AND $filtre['domaine'] != '') {
            $qb = $qb->andWhere('domaine.id = :domaine')->setParameter('domaine', $filtre['domaine']);
        }

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    /**
     * @param int $niveau
     * @return FicheMetier[]
     */
    public function getFichesMetiersWithNiveau(int $niveau) : array
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('niveauxbas.niveau >= :niveau')
            ->andWhere('niveauxhaut.niveau <= :niveau')
            ->setParameter('niveau', $niveau)
            ->andWhere('ficheMetier.histoDestruction IS NULL')
            ->andWhere('etat.code = :ok')
            ->setParameter('ok', FicheMetierEtats::ETAT_VALIDE)
        ;

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    /**
     * @param string $order an attribute use to sort
     * @return FicheMetier[]
     */
    public function getFichesMetiersValides(string $order = 'id') : array
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('etat.code = :ucode')
            ->setParameter('ucode', FicheMetierEtats::ETAT_VALIDE)
            ->orderBy('ficheMetier.', $order)
        ;

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    /**
     * @param int|null $id
     * @return FicheMetier
     */
    public function getFicheMetier(?int $id) : ?FicheMetier
    {
        $qb = $this->createQueryBuilder()
            ->addSelect('fmactivite')->leftJoin('ficheMetier.activites', 'fmactivite')
//            ->addSelect('activite')->leftJoin('fmactivite.activite', 'activite')
//            ->addSelect('activite_libelle')->leftJoin('activite.libelles', 'activite_libelle')
//            ->addSelect('activite_dscription')->leftJoin('activite.descriptions', 'activite_dscription')
//            ->addSelect('aformation')->leftJoin('activite.formations', 'aformation')

            //APPLICATIONS - fiche et activités associées
//            ->addSelect('activite_applicationelement')->leftJoin('activite.applications', 'activite_applicationelement')
//            ->addSelect('activite_application')->leftJoin('activite_applicationelement.application', 'activite_application')
//            ->addSelect('activite_application_groupe')->leftJoin('activite_application.groupe', 'activite_application_groupe')
//            ->addSelect('fiche_applicationelement')->leftJoin('ficheMetier.applications', 'fiche_applicationelement')
//            ->addSelect('fiche_application')->leftJoin('fiche_applicationelement.application', 'fiche_application')
//            ->addSelect('fiche_application_niveau')->leftJoin('fiche_applicationelement.niveau', 'fiche_application_niveau')
//            ->addSelect('fiche_application_groupe')->leftJoin('fiche_application.groupe', 'fiche_application_groupe')

            //COMPETENCE - fiche et activités associées
//            ->addSelect('activite_competenceelement')->leftJoin('activite.competences', 'activite_competenceelement')
//            ->addSelect('activite_competence')->leftJoin('activite_competenceelement.competence', 'activite_competence')
//            ->addSelect('activite_competence_theme')->leftJoin('activite_competence.theme', 'activite_competence_theme')
//            ->addSelect('activite_competence_type')->leftJoin('activite_competence.type', 'activite_competence_type')
//            ->addSelect('fiche_competenceelement')->leftJoin('ficheMetier.competences', 'fiche_competenceelement')
//            ->addSelect('fiche_competence')->leftJoin('fiche_competenceelement.competence', 'fiche_competence')
//            ->addSelect('fiche_competence_niveau')->leftJoin('fiche_competenceelement.niveau', 'fiche_competence_niveau')
//            ->addSelect('fiche_competence_theme')->leftJoin('fiche_competence.theme', 'fiche_competence_theme')
//            ->addSelect('fiche_competence_type')->leftJoin('fiche_competence.type', 'fiche_competence_type')

            ->addSelect('categorie')->leftJoin('metier.categorie', 'categorie')
            ->andWhere('ficheMetier.id = :id')
            ->setParameter('id', $id)
        ;

        try {
            $result = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new RuntimeException("Plusieurs fiches métiers portent le même identifiant [".$id."].");
        }
        return $result;
    }

    /**
     * @param AbstractController $controller
     * @param string $name
     * @param bool $notNull
     * @return FicheMetier|null
     */
    public function getRequestedFicheMetier(AbstractController $controller, string $name = 'fiche', bool $notNull = false) : ?FicheMetier
    {
        $ficheId = $controller->params()->fromRoute($name);
        $fiche = $this->getFicheMetier($ficheId);
        if($notNull && !$fiche) throw new RuntimeException("Aucune fiche de trouvée avec l'identifiant [".$ficheId."]");

        return $fiche;
    }

    /**
     * @param Domaine $domaine
     * @return FicheMetier[]
     */
    public function getFicheByDomaine(Domaine $domaine) : array
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('domaine = :domaine')
            ->setParameter('domaine', $domaine)
            ->orderBy('metier.libelle')
        ;

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    /**
     * @return array
     */
    public function getFichesMetiersAsOptionGroup() : array
    {
        $domaines = $this->getDomaineService()->getDomaines();
        $options = [];

        foreach ($domaines as $domaine) {
            $optionsoptions = [];
            foreach ($this->getFicheByDomaine($domaine) as $fiche) {
                if ($fiche->estNonHistorise()) $optionsoptions[$fiche->getId()] = $fiche->getMetier()->getLibelle() . " (dernière modification ".$fiche->getHistoModification()->format("d/m/Y").")";
            }
            asort($optionsoptions);
            $array = [
                'label' => $domaine->getLibelle(),
                'options' => $optionsoptions,
            ];
            $options[] = $array;
        }

        return $options;
    }

    /** FACADE ********************************************************************************************************/

    public function setDefaultValues(FicheMetier $fiche) : FicheMetier
    {
        $fiche->setEtat($this->getEtatService()->getEtatByCode(FicheMetierEtats::ETAT_REDACTION));
        $this->getConfigurationService()->addDefaultToFicheMetier($fiche);
        return $fiche;
    }

    /**
     * @param Request $request
     * @param Form $form
     * @param $service
     */
    public function updateFromForm(Request $request, Form $form, $service)
    {
        $data = $request->getPost();
        $form->setData($data);
        if ($form->isValid()) {
            $service->update($form->getObject());
        }
    }


    /**
     * @param FicheMetier $fiche
     * @param bool $asElement
     * @return array
     */
    public function getApplicationsDictionnaires(FicheMetier $fiche, bool $asElement = false) : array
    {
        $dictionnaire = [];

        foreach ($fiche->getApplicationListe() as $applicationElement) {
            $application = ($asElement)?$applicationElement:$applicationElement->getApplication();
            $dictionnaire[$application->getId()]["entite"] = $application;
            $dictionnaire[$application->getId()]["raison"][] = $fiche;
            $dictionnaire[$application->getId()]["conserve"] = true;
        }

        foreach ($fiche->getActivites() as $activite) {
            foreach ($activite->getActivite()->getApplicationListe() as $applicationElement) {
                $application = ($asElement)?$applicationElement:$applicationElement->getApplication();
                $dictionnaire[$application->getId()]["entite"] = $application;
                $dictionnaire[$application->getId()]["raison"][] = $activite;
                $dictionnaire[$application->getId()]["conserve"] = true;
            }
        }

        return $dictionnaire;
    }

    /**
     * @param FicheMetier $fiche
     * @param bool $asElement
     * @return array
     */
    public function getCompetencesDictionnaires(FicheMetier $fiche, bool $asElement = false) : array
    {
        $dictionnaire = [];

        foreach ($fiche->getCompetenceListe() as $competenceElement) {
            $competence = ($asElement)?$competenceElement:$competenceElement->getCompetence();
            $dictionnaire[$competence->getId()]["entite"] = $competence;
            $dictionnaire[$competence->getId()]["raison"][] = $fiche;
            $dictionnaire[$competence->getId()]["conserve"] = true;
        }

        foreach ($fiche->getActivites() as $activite) {
            foreach ($activite->getActivite()->getCompetenceListe() as $competenceElement) {
                $competence = ($asElement)?$competenceElement:$competenceElement->getCompetence();
                $dictionnaire[$competence->getId()]["entite"] = $competence;
                $dictionnaire[$competence->getId()]["raison"][] = $activite;
                $dictionnaire[$competence->getId()]["conserve"] = true;
            }
        }
        return $dictionnaire;
    }

    /**
     * @param Competence $competence
     * @return FicheMetier[]
     */
    public function getFichesMetiersByCompetence(Competence $competence) : array
    {
        $qb = $this->createQueryBuilder()
            ->addSelect('fiche_competenceelement')->leftJoin('ficheMetier.competences', 'fiche_competenceelement')
            ->addSelect('fiche_competence')->leftJoin('fiche_competenceelement.competence', 'fiche_competence')
            ->andWhere('fiche_competenceelement.competence = :competence')
            ->setParameter('competence', $competence)
            ->orderBy('metier.libelle', 'ASC')
        ;

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    /**
     * @param FicheMetier $fiche
     * @return FicheMetier
     */
    public function dupliquerFicheMetier(FicheMetier $fiche) : FicheMetier
    {
        $duplicata = new FicheMetier();
        //base
        $duplicata->setMetier($fiche->getMetier());
        $duplicata->setExpertise($fiche->hasExpertise());
        $this->create($duplicata);

        //missions principales
        /** @var FicheMetierActivite $activite */
        foreach ($fiche->getActivites() as $activite) {
            $activiteDuplicata = new FicheMetierActivite();
            $activiteDuplicata->setActivite($activite->getActivite());
            $activiteDuplicata->setPosition($activite->getPosition());
            $activiteDuplicata->setFiche($duplicata);
            try {
                $this->getEntityManager()->persist($activiteDuplicata);
                $this->getEntityManager()->flush($activiteDuplicata);
            } catch (ORMException $e) {
                throw new RuntimeException("Un problème est survenu lors de la duplication d'un activité");
            }
        }

        //applications
        /** @var ApplicationElement $application */
        foreach ($fiche->getApplicationCollection() as $application) {
            $element = new ApplicationElement();
            $element->setApplication($application->getApplication());
            $element->setCommentaire($application->getCommentaire());
            $element->setClef($application->isClef());
            $this->getApplicationElementService()->create($element);
            if ($application->estHistorise()) {
                $this->getApplicationElementService()->historise($element);
                $this->getApplicationElementService()->update($element);
            }
            $duplicata->addApplicationElement($element);
        }

        //compétences
        /** @var CompetenceElement $competence */
        foreach ($fiche->getCompetenceCollection() as $competence) {
            $element = new CompetenceElement();
            $element->setCompetence($competence->getCompetence());
            $element->setCommentaire($competence->getCommentaire());
            $element->setClef($competence->isClef());
            $this->getCompetenceElementService()->create($element);
            if ($competence->estHistorise()) {
                $this->getCompetenceElementService()->historise($element);
                $this->getCompetenceElementService()->update($element);
            }
            $duplicata->addCompetenceElement($element);
        }

        //etat
        $duplicata->setEtat($this->getEtatService()->getEtatByCode(FicheMetierEtats::ETAT_REDACTION));
        $this->update($duplicata);

        return $duplicata;
    }

    public function readFromCSV($fichier_path) : array
    {
        $handle = fopen($fichier_path, "r");

        $array = [];
        while ($content = fgetcsv ( $handle, 0, ";")) {
            $array[] = $content;
        }

        $code_index = array_search('Code emploi type', $array[0]);
        $code_libelle =  $array[1][$code_index];
        $metier = $this->getMetierService()->getMetierByReference('REFERENS', $code_libelle);
        $mission_index = array_search('Mission', $array[0]);
        $mission_libelle = $array[1][$mission_index];
        $activites_index = array_search('Activités principales', $array[0]);
        $activites_libelle = explode(FicheMetierController::REFERENS_SEP ,$array[1][$activites_index]);

        $competences_index = array_search('COMPETENCES_ID', $array[0]);
        $competences_ids   = explode(FicheMetierController::REFERENS_SEP ,$array[1][$competences_index]);

        $competences['Connaissances'] = [];
        $competences['Opérationnelles'] = [];
        $competences['Comportementales'] = [];
        $competences['Manquantes'] = [];
        $competencesListe = [];
        foreach ($competences_ids as $competence_id) {
            $competence = $this->getCompetenceService()->getCompetenceByIdSource('REFERENS 3', $competence_id);
            if ($competence !== null) {
                $competences[$competence->getType()->getLibelle()][$competence->getId()] = $competence;
                $competencesListe[$competence->getId()] = $competence;
            } else {
                $competences['Manquantes'][] = $competence_id;
            }
        }
        $applications = [];

        return [
            'code' => $code_libelle,
            'metier' => $metier,
            'mission' => $mission_libelle,
            'activites' => $activites_libelle,
            'competences' => $competences,
            'competencesListe' => $competencesListe,
            'applications' => $applications,

        ];
    }

    public function importFromCsvArray(array $csvInfos) : FicheMetier
    {
        //init
        $fiche = new FicheMetier();
        $fiche->setMetier($csvInfos['metier']);
        $fiche->setEtat($this->getEtatService()->getEtatByCode(FicheMetierEtats::ETAT_REDACTION));
        $this->create($fiche);

        // MISSIONS PRINCIPALES
        $activite = new Activite();
        $this->getActiviteService()->create($activite);
        $this->getActiviteService()->updateLibelle($activite, ['libelle' => $csvInfos['metier']]);
        foreach ($csvInfos['activites'] as $libelle) {
            $description = new ActiviteDescription();
            $description->setActivite($activite);
            $description->setDescription($libelle);
            $this->getActiviteDescriptionService()->create($description);
        }
        $this->getActiviteService()->createFicheMetierActivite($fiche, $activite);

        //APPLICATION
        $this->getHasApplicationCollectionService()->updateApplications($fiche, ['applications' => $csvInfos['applications']]);

        //COMPETENCE
        $this->getHasCompetenceCollectionService()->updateCompetences($fiche, ['competences' => $csvInfos['competencesListe']]);

        return $fiche;
    }

    public function exporter(?FicheMetier $fichemetier)
    {
        $vars = [
            'fichemetier' => $fichemetier,
            'metier' => $fichemetier->getMetier(),
        ];
        $rendu = $this->getRenduService()->generateRenduByTemplateCode(PdfTemplate::FICHE_METIER, $vars);

        try {
            $exporter = new PdfExporter();
            $exporter->getMpdf()->SetTitle($rendu->getSujet());
            $exporter->setHeaderScript('');
            $exporter->setFooterScript('');
            $exporter->addBodyHtml($rendu->getCorps());
            return $exporter->export($rendu->getSujet());
        } catch (MpdfException $e) {
            throw new RuntimeException("Un problème est survenu lors de l'export en PDF", 0, $e);
        }
    }

}