<?php

namespace Formation\Controller;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Formation\Entity\Db\EnqueteCategorie;
use Formation\Entity\Db\EnqueteQuestion;
use Formation\Entity\Db\EnqueteReponse;
use Formation\Form\EnqueteCategorie\EnqueteCategorieFormAwareTrait;
use Formation\Form\EnqueteQuestion\EnqueteQuestionFormAwareTrait;
use Formation\Form\EnqueteReponse\EnqueteReponseFormAwareTrait;
use Formation\Service\EnqueteCategorie\EnqueteCategorieServiceAwareTrait;
use Formation\Service\EnqueteQuestion\EnqueteQuestionServiceAwareTrait;
use Formation\Service\EnqueteReponse\EnqueteReponseServiceAwareTrait;
use Formation\Service\FormationInstance\FormationInstanceServiceAwareTrait;
use Formation\Service\Inscription\InscriptionServiceAwareTrait;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class EnqueteQuestionController extends AbstractActionController
{
    use ProvidesObjectManager;
    use EnqueteCategorieServiceAwareTrait;
    use EnqueteQuestionServiceAwareTrait;
    use EnqueteReponseServiceAwareTrait;
    use FormationInstanceServiceAwareTrait;
    use EnqueteCategorieFormAwareTrait;
    use EnqueteQuestionFormAwareTrait;
    use EnqueteReponseFormAwareTrait;
    use InscriptionServiceAwareTrait;

    /** ENQUETE *******************************************************************************************************/

    public function afficherResultatsAction(): ViewModel
    {
        $session = $this->getInscriptionService()->getRequestedInscription($this);

        $questions = $this->getObjectManager()->getRepository(EnqueteQuestion::class)->findAll();
        $questions = array_filter($questions, function (EnqueteQuestion $a) {
            return $a->estNonHistorise();
        });
        usort($questions, function (EnqueteQuestion $a, EnqueteQuestion $b) {
            return $a->getOrdre() <=> $b->getOrdre();
        });

        $reponses = $this->getObjectManager()->getRepository(EnqueteReponse::class)->findAll();

        //todo exploiter le filtre pour réduire
        if ($session) $reponses = array_filter($reponses, function (EnqueteReponse $r) use ($session) {
            return $r->getInscription()->getSession() === $session;
        });
        //todo fin

        $reponses = array_filter($reponses, function (EnqueteReponse $a) {
            return $a->estNonHistorise();
        });
        usort($reponses, function (EnqueteReponse $a, EnqueteReponse $b) {
            return $a->getQuestion()->getId() <=> $b->getQuestion()->getId();
        });

        /** PREP HISTOGRAMME $histogramme */
        $histogramme = [];
        foreach ($questions as $question) {
            $histogramme[$question->getId()] = [];
            foreach (EnqueteReponse::NIVEAUX as $clef => $value) $histogramme[$question->getId()][$clef] = 0;
        }

        $array = [];

        /** @var EnqueteReponse $reponse */
        foreach ($reponses as $reponse) {
            if ($reponse->getQuestion()->estNonHistorise()) {
                $question = $reponse->getQuestion()->getId();
                $inscription = $reponse->getInscription()->getId();

                $niveau = $reponse->getNiveau();
                $description = $reponse->getDescription();

                $array[$inscription]["Niveau_" . $question] = EnqueteReponse::NIVEAUX[$niveau];
                $array[$inscription]["Commentaire_" . $question] = $description;
                $histogramme[$question][$niveau]++;
            }
        }

        $categories = $this->getObjectManager()->getRepository(EnqueteCategorie::class)->findAll();
        return new ViewModel([
            "array" => $array,
            "histogramme" => $histogramme,
            "nbReponses" => count($array),
            "questions" => $questions,
            "categories" => $categories,
        ]);
    }

    /** QUESTIONS *****************************************************************************************************/

    public function afficherQuestionsAction(): ViewModel
    {
        $questions = $this->getObjectManager()->getRepository(EnqueteQuestion::class)->findAll();
        $categories = $this->getObjectManager()->getRepository(EnqueteCategorie::class)->findAll();

        return new ViewModel([
            'categories' => $categories,
            'questions' => $questions,
        ]);
    }

    public function ajouterCategorieAction(): ViewModel
    {
        $question = new EnqueteCategorie();

        $form = $this->getEnqueteCategorieForm();
        $form->setAttribute('action', $this->url()->fromRoute('formation/enquete/categorie/ajouter', [], [], true));
        $form->bind($question);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getEnqueteCategorieService()->create($question);
            }
        }

        $vm = new ViewModel([
            'title' => "Ajout d'une catégorie",
            'form' => $form,
        ]);
        $vm->setTemplate('formation/default/default-form');
        return $vm;
    }

    public function modifierCategorieAction(): ViewModel
    {
        $categorie = $this->getEnqueteCategorieService()->getRequestedEnqueteCategorie($this);

        $form = $this->getEnqueteCategorieForm();
        $form->setAttribute('action', $this->url()->fromRoute('formation/enquete/categorie/modifier', ['categorie' => $categorie->getId()], [], true));
        $form->bind($categorie);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getEnqueteCategorieService()->update($categorie);
            }
        }

        $vm = new ViewModel([
            'title' => "Modification de la catégorie",
            'form' => $form,
        ]);
        $vm->setTemplate('formation/default/default-form');
        return $vm;
    }

    public function historiserCategorieAction(): Response
    {
        $categorie = $this->getEnqueteCategorieService()->getRequestedEnqueteCategorie($this);
        $this->getEnqueteCategorieService()->historise($categorie);

        $retour = $this->params()->fromQuery('retour');
        if ($retour) return $this->redirect()->toUrl($retour);
        return $this->redirect()->toRoute('formation/enquete/question', [], [], true);
    }

    public function restaurerCategorieAction(): Response
    {
        $categorie = $this->getEnqueteCategorieService()->getRequestedEnqueteCategorie($this);
        $this->getEnqueteCategorieService()->restore($categorie);

        $retour = $this->params()->fromQuery('retour');
        if ($retour) return $this->redirect()->toUrl($retour);
        return $this->redirect()->toRoute('formation/enquete/question', [], [], true);
    }

    public function supprimerCategorieAction()
    {
        /** @var EnqueteCategorie|null $categorie */
        $categorie = $this->getEnqueteCategorieService()->getRequestedEnqueteCategorie($this);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if ($data["reponse"] === "oui") $this->getEnqueteCategorieService()->delete($categorie);
            exit();
        }

        $vm = new ViewModel();
        if ($categorie !== null) {
            $vm->setTemplate('formation/default/confirmation');
            $vm->setVariables([
                'title' => "Suppression de la catégorie",
                'text' => "La suppression est définitive êtes-vous sûr&middot;e de vouloir continuer ?",
                'action' => $this->url()->fromRoute('formation/enquete/categorie/supprimer', ["categorie" => $categorie->getId()], [], true),
            ]);
        }
        return $vm;
    }

    public function ajouterQuestionAction(): ViewModel
    {

        $question = new EnqueteQuestion();

        $form = $this->getEnqueteQuestionForm();
        $form->setAttribute('action', $this->url()->fromRoute('formation/enquete/question/ajouter', [], [], true));
        $form->bind($question);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getEnqueteQuestionService()->create($question);
            }
        }

        $vm = new ViewModel([
            'title' => "Ajout d'une question",
            'form' => $form,
        ]);
        $vm->setTemplate('formation/default/default-form');
        return $vm;
    }

    public function modifierQuestionAction(): ViewModel
    {
        $question = $this->getEnqueteQuestionService()->getRequestedEnqueteQuestion($this);

        $form = $this->getEnqueteQuestionForm();
        $form->setAttribute('action', $this->url()->fromRoute('formation/enquete/question/modifier', ['question' => $question->getId()], [], true));
        $form->bind($question);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->getEnqueteQuestionService()->update($question);
            }
        }

        $vm = new ViewModel([
            'title' => "Modification de la question",
            'form' => $form,
        ]);
        $vm->setTemplate('formation/default/default-form');
        return $vm;
    }

    public function historiserQuestionAction(): Response
    {
        $question = $this->getEnqueteQuestionService()->getRequestedEnqueteQuestion($this);
        $this->getEnqueteQuestionService()->historise($question);

        $retour = $this->params()->fromQuery('retour');
        if ($retour) return $this->redirect()->toUrl($retour);
        return $this->redirect()->toRoute('formation/enquete/question', [], [], true);
    }

    public function restaurerQuestionAction(): Response
    {
        $question = $this->getEnqueteQuestionService()->getRequestedEnqueteQuestion($this);
        $this->getEnqueteQuestionService()->restore($question);

        $retour = $this->params()->fromQuery('retour');
        if ($retour) return $this->redirect()->toUrl($retour);
        return $this->redirect()->toRoute('formation/enquete/question', [], [], true);
    }

    public function supprimerQuestionAction(): ViewModel
    {
        $question = $this->getEnqueteQuestionService()->getRequestedEnqueteQuestion($this);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if ($data["reponse"] === "oui") $this->getEnqueteQuestionService()->delete($question);
            exit();
        }

        $vm = new ViewModel();
        if ($question !== null) {
            $vm->setTemplate('formation/default/confirmation');
            $vm->setVariables([
                'title' => "Suppression de la question",
                'text' => "La suppression est définitive êtes-vous sûr&middot;e de vouloir continuer ?",
                'action' => $this->url()->fromRoute('formation/enquete/question/supprimer', ["question" => $question->getId()], [], true),
            ]);
        }
        return $vm;
    }

    /** REPONSES ******************************************************************************************************/

    public function repondreQuestionsAction(): ViewModel
    {
        $inscription = $this->getInscriptionService()->getRequestedInscription($this);

        /** @var EnqueteQuestion[] $questions */
        $questions = $this->getEnqueteQuestionService()->getEnqueteQuestions();

        $dictionnaireQuestion = [];
        foreach ($questions as $question) $dictionnaireQuestion[$question->getId()] = $question;
        usort($questions, function (EnqueteQuestion $a, EnqueteQuestion $b) {
            return $a->getOrdre() <=> $b->getOrdre();
        });

        $reponses = $this->getEnqueteReponseService()->findEnqueteReponseByInscription($inscription);
        $reponses = array_filter($reponses, function (EnqueteReponse $a) {
            return $a->estNonHistorise();
        });
        $dictionnaireReponse = [];
        foreach ($reponses as $reponse) $dictionnaireReponse[$reponse->getQuestion()->getId()] = $reponse;
        $enquete = new ArrayCollection();
        foreach ($dictionnaireQuestion as $id => $question) {
            if (! isset($dictionnaireReponse[$id])) {
                $reponse = new EnqueteReponse();
                $reponse->setInscription($inscription);
                $reponse->setQuestion($question);
            } else {
                $reponse = $dictionnaireReponse[$id];
            }
            $element = [$question, $reponse];
            $enquete->add($element);
        }

        $form = $this->getEnqueteReponseForm();
        $form->setAttribute('action', $this->url()->fromRoute('formation/enquete/repondre-questions', ['inscription' => $inscription->getId()], [], true));
        $form->bind($enquete);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                foreach ($enquete as $element) {
                    [$question, $reponse] = $element;
                    if ($reponse->getHistoCreation()) $this->getEnqueteReponseService()->update($reponse);
                    else $this->getEnqueteReponseService()->create($reponse);
                }
            }
        }

        $categories = $this->getEnqueteCategorieService()->getEnqueteCateories();

        return new ViewModel([
            'inscription' => $inscription,
            'questions' => $questions,
            'categories' => $categories,
            'form' => $form,
        ]);
    }

    public function validerQuestionsAction(): ViewModel
    {
        $inscription = $this->getInscriptionService()->getRequestedInscription($this);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if ($data["reponse"] === "oui") {
                $inscription->setValidationEnquete(new DateTime());
                $this->getInscriptionService()->update($inscription);
            }
            exit();
        }

        $vm = new ViewModel();
        if ($inscription !== null) {
            $vm->setTemplate('formation/default/confirmation');
            $vm->setVariables([
                'title' => "Validation de l'enquête",
                'text' => "La validation est définitive, après celle-ci vous ne pourrez pas revenir sur l'enquête. <br/> Êtes-vous sûr·e de vouloir continuer ?",
                'action' => $this->url()->fromRoute('formation/enquete/valider-questions', ["inscription" => $inscription->getId()], [], true),
                'confirmation' => false,
                'complement' => false,
            ]);
        }
        return $vm;
    }
}