<?php

namespace Application\Form\AgentMissionSpecifique;

use Application\Service\MissionSpecifique\MissionSpecifiqueServiceAwareTrait;
use UnicaenApp\Form\Element\SearchAndSelect;
use Zend\Form\Element\Button;
use Zend\Form\Element\Date;
use Zend\Form\Element\DateTime;
use Zend\Form\Element\Number;
use Zend\Form\Element\Select;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class AgentMissionSpecifiqueForm extends Form {
    use MissionSpecifiqueServiceAwareTrait;

    /** @var string */
    private $urlAgent;

    /**
     * @param string $urlAgent
     * @return AgentMissionSpecifiqueForm
     */
    public function setUrlAgent(string $urlAgent): AgentMissionSpecifiqueForm
    {
        $this->urlAgent = $urlAgent;
        return $this;
    }

    /** @var string */
    private $urlStructure;

    /**
     * @param string $url
     * @return AgentMissionSpecifiqueForm
     */
    public function setUrlStructure($url)
    {
        $this->urlStructure = $url;
        return $this;
    }

    public function init()
    {
        //Mission
        $this->add([
            'type' => Select::class,
            'name' => 'mission',
            'options' => [
                'label' => "Mission * :",
                'empty_option' => 'Sélectionner la mission à affecter ...',
                'value_options' => $this->getMissionSpecifiqueService()->getMisssionsSpecifiquesAsGroupOptions(),
            ],
            'attributes' => [
                'id' => 'mission',
                'class'             => 'bootstrap-selectpicker show-tick',
                'data-live-search'  => 'true',
            ],
        ]);

        //Agent
        $agent = new SearchAndSelect('agent', ['label' => "Agent * :"]);
        $agent
            ->setAutocompleteSource($this->urlAgent)
            ->setSelectionRequired(true)
            ->setAttributes([
                'id' => 'agent',
                'placeholder' => "Agent effectuant la mission ...",
            ]);
        $this->add($agent);

        // structure
        $structure = new SearchAndSelect('structure', ['label' => "Service/composante/direction d'affectation * :"]);
        $structure
            ->setAutocompleteSource($this->urlStructure)
            ->setSelectionRequired(true)
            ->setAttributes([
                'id' => 'structure',
                'placeholder' => "Nom de la structure ...",
            ]);
        $this->add($structure);

        //Debut
        $this->add([
            'type' => Date::class,
            'name' => 'debut',
            'options' => [
                'label' => "Date de début* :",
                'format' => "d/m/Y",
            ],
            'attributes' => [
                'id' => 'debut',
            ],
        ]);

        //Fin
        $this->add([
            'type' => Date::class,
            'name' => 'fin',
            'options' => [
                'label' => "Date de fin :",
                'format' => "d/m/Y",
            ],
            'attributes' => [
                'id' => 'fin',
            ],
        ]);
        //Decharge
        $this->add([
            'type' => Number::class,
            'name' => 'decharge',
            'options' => [
                'label' => "Volume horaire associé à la mission pour une année complète  :",
            ],
            'attributes' => [
                'id' => 'decharge',
            ],
        ]);

        //Submit
        $this->add([
            'type' => Button::class,
            'name' => 'creer',
            'options' => [
                'label' => '<i class="fas fa-save"></i> Enregistrer affectation',
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
            'agent'             => [ 'required' => true,  ],
            'mission'           => [ 'required' => true,  ],
            'structure'         => [ 'required' => false, ],
            'debut'             => [ 'required' => true,  ],
            'fin'               => [ 'required' => false, ],
            'decharge'               => [ 'required' => false, ],
        ]));
    }
}