<?php

namespace Application\Form\AgentApplication;

use Application\Service\Application\ApplicationServiceAwareTrait;
use Zend\Form\Element\Button;
use Zend\Form\Element\Select;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class AgentApplicationForm extends Form {
    use ApplicationServiceAwareTrait;

    public function init()
    {
        //competence
        $this->add([
            'name' => 'application',
            'type' => Select::class,
            'options' => [
                'label' => 'Application * : ',
                'label_attributes' => [
                    'class' => 'control-label',
                ],
                'empty_option' => "Sélectionner une compétence ... ",
                'value_options' => $this->getApplicationService()->getApplicationsAsOptions(),
            ],
            'attributes' => [
                'id'                => 'competence',
                'class'             => 'bootstrap-selectpicker show-tick',
                'data-live-search'  => 'true',
            ]
        ]);
        //niveau
        $this->add([
            'name' => 'type',
            'type' => Select::class,
            'options' => [
                'label' => 'Type de formation * : ',
                'label_attributes' => [
                    'class' => 'control-label',
                ],
                'empty_option' => "Sélectionner un type de formation ... ",
                'value_options' => [
                    'Autoformation'          => "Autoformation",
                    'Formation externe'       => "Formation externe",
                    'Formation interne'       => "Formation interne",
                ],
            ],
            'attributes' => [
                'id'                => 'type',
                'class'             => 'bootstrap-selectpicker show-tick',
                'data-live-search'  => 'true',
            ]
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
            'application' => [
                'required' => true,
            ],
            'type' => [
                'required' => true,
            ],
        ]));
    }
}