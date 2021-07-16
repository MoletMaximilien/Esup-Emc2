<?php

namespace Application\Form\Niveau;

use Zend\Form\Element\Button;
use Zend\Form\Element\Number;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class NiveauForm extends Form {

    public function init()
    {
        //niveau
        $this->add([
            'type' => Number::class,
            'name' => 'niveau',
            'options' => [
                'label' => "Niveau * :",
            ],
            'attributes' => [
                'id' => 'niveau',
                'min'  => '1',
                'max'  => '5',
                'step' => '1',
            ],
        ]);
        //libelle
        //niveau
        $this->add([
            'type' => Text::class,
            'name' => 'libelle',
            'options' => [
                'label' => "Libelle * :",
            ],
            'attributes' => [
                'id' => 'libelle',
            ],
        ]);
        // description
        $this->add([
            'name' => 'description',
            'type' => 'textarea',
            'options' => [
                'label' => 'Description : ',
                'label_attributes' => [
                    'class' => 'control-label',
                ],
            ],
            'attributes' => [
                'class' => 'type2 form-control',
            ]
        ]);
        //button
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
            'niveau'                => [ 'required' => true,  ],
            'libelle'               => [ 'required' => true,  ],
            'description'           => [ 'required' => false,  ],
        ]));
    }
}