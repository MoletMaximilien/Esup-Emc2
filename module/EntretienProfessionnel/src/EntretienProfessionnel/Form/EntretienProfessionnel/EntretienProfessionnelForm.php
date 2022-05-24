<?php

namespace EntretienProfessionnel\Form\EntretienProfessionnel;

use DateTime;
use EntretienProfessionnel\Entity\Db\EntretienProfessionnel;
use EntretienProfessionnel\Service\Campagne\CampagneServiceAwareTrait;
use UnicaenApp\Form\Element\SearchAndSelect;
use Zend\Form\Element\Button;
use Zend\Form\Element\Date;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Element\Time;
use Zend\Form\Form;
use Zend\InputFilter\Factory;
use Zend\Validator\Callback;

class EntretienProfessionnelForm extends Form {
    use CampagneServiceAwareTrait;

    private $urlAgent;
    private $urlResponsable;

    /**
     * @param mixed $urlAgent
     * @return EntretienProfessionnelForm
     */
    public function setUrlAgent($urlAgent) : EntretienProfessionnelForm
    {
        $this->urlAgent = $urlAgent;
        return $this;
    }

    /**
     * @param mixed $urlResponsable
     * @return EntretienProfessionnelForm
     */
    public function setUrlResponsable($urlResponsable) : EntretienProfessionnelForm
    {
        $this->urlResponsable = $urlResponsable;
        return $this;
    }

    public function init()
    {
        //Responsable
        $responsable = new SearchAndSelect('responsable', ['label' => "Responsable de l'entretien professionnel * :"]);
        $responsable
            ->setAutocompleteSource($this->urlResponsable)
            ->setSelectionRequired(true)
            ->setAttributes([
                'id' => 'responsable',
                'placeholder' => "Nom du responsable de l'entretien professionnel ...",
            ]);
        $this->add($responsable);
        //Agent
        $agent = new SearchAndSelect('agent', ['label' => "Agent * :"]);
        $agent
            ->setAutocompleteSource($this->urlAgent)
            ->setSelectionRequired(true)
            ->setAttributes([
                'id' => 'agent',
                'placeholder' => "Nom de l'agent ...",
            ]);
        $this->add($agent);
        //CAMPAGNE (SELECT)
        $this->add([
            'name' => 'campagne',
            'type' => Select::class,
            'options' => [
                'label' => 'Campagne * : ',
                'label_attributes' => [
                    'class' => 'control-label',
                ],
                'empty_option' => "Sélectionner une campagne ... ",
                'value_options' => $this->getCampagneService()->getCampagnesAsOptions(),
            ],
            'attributes' => [
                'id'                => 'campagne',
                'class'             => 'bootstrap-selectpicker show-tick',
                'data-live-search'  => 'true',
            ]
        ]);

        //Date        (initialisée à la date du jour)
        $this->add([
            'type' => Date::class,
            'name' => 'date_entretien',
            'options' => [
                'label' => "Date de l'entretien* :",
//                'format' => 'd/m/Y',
            ],
            'attributes' => [
                'id' => 'date_entretien',
            ],
        ]);

        //Heure        (initialisée à la date du jour)
        $this->add([
            'type' => Time::class,
            'name' => 'heure_entretien',
            'options' => [
                'label' => "Heure de l'entretien* :",
                'format' => 'H:i',
            ],
            'attributes' => [
                'id' => 'heure_entretien',
            ],
        ]);

        //Lieu
        $this->add([
            'type' => Text::class,
            'name' => 'lieu_entretien',
            'options' => [
                'label' => "Lieu de l'entretien * :",
            ],
            'attributes' => [
                'id' => 'lieu_entretien',
            ],
        ]);

        // button
        $this->add([
            'type' => Button::class,
            'name' => 'creer',
            'options' => [
                'label' => '<i class="fas fa-save"></i> Enregistrer',
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-primary',
            ],
        ]);

        $this->setInputFilter((new Factory())->createInputFilter([
            'responsable'       => [
                'required' => true,
                'validators' => [
                    [
                        'name' => Callback::class,
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => "Veuillez sélectionner un responsable dans la liste déroulante.",
                            ],
                            'callback' => function ($value, $context = []) {
                                $hasResponsable = (isset($context['responsable']) AND isset($context['responsable']['id']) AND trim($context['responsable']['id']) !== '');
                                return $hasResponsable;
                            },
                        ],
                    ],
                ],
            ],
            'agent'             => [
                'required' => true,
                'validators' => [
                    [
                        'name' => Callback::class,
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => "Veuillez sélectionnez un responsable dans la liste déroulante.",
                            ],
                            'callback' => function ($value, $context = []) {
                                $hasResponsable = (isset($context['agent']) AND isset($context['agent']['id']) AND trim($context['agent']['id']) !== '');
                                return $hasResponsable;
                            },
                        ],
                    ],
                ],
            ],
            'campagne'          => [ 'required' => true,  ],
            'date_entretien'    => [
                'required' => true,
                'validators' => [
                    [
                        'name' => Callback::class,
                        'options' => [
                            'messages' => [
                                Callback::INVALID_VALUE => "L'entretien doit être conduit durant la campagne",
                            ],
                            'callback' => function ($value, $context = []) {
                                /** @var EntretienProfessionnel $entretien */
                                $entretien = $this->getObject();
                                $campagne = $entretien->getCampagne();
                                $date = DateTime::createFromFormat('Y-m-d', $context['date_entretien']);
                                $res =  ($campagne->getDateDebut() <= $date AND $campagne->getDateFin() >= $date);
                                return $res;
                            },
                        ],
                    ],
//                    [
//                        'name' => Callback::class,
//                        'options' => [
//                            'messages' => [
//                                Callback::INVALID_VALUE => "L'entretien doit être conduit au minimum 15 jours après l'envoi de la convocation",
//                            ],
//                            'callback' => function ($value, $context = []) {
//                                /** @var EntretienProfessionnel $entretien */
//                                $maintenant = new DateTime();
//                                $maintenant = $maintenant->add(new DateInterval('P14D'));
//                                $date = DateTime::createFromFormat('Y-m-d', $context['date_entretien']);
//                                $res =  ($maintenant <= $date);
//                                return $res;
//                            },
//                        ],
//                    ],
                ],
            ],
            'heure_entretien'          => [ 'required' => true,  ],
            'lieu_entretien'           => [ 'required' => true,  ],
        ]));
    }
}
