<?php

namespace Application\Form\Complement;

use UnicaenApp\Form\Element\SearchAndSelect;
use Zend\Form\Element\Button;
use Zend\Form\Element\Text;
use Zend\Form\Form;

class ComplementForm extends Form {

    public function init()
    {
        //Search & Select
        /**
         * SearchAndSelect sur les Individus de la structure fictives
         */
        $sas = new SearchAndSelect('sas', ['label' => "Complément :"]);
        $sas
            ->setSelectionRequired(true)
            ->setAttributes([
                'id' => 'sas',
                'placeholder' => "Sélectionner votre compléments ... ",
            ]);
        $this->add($sas);

        //Text
        $this->add([
            'type' => Text::class,
            'name' => 'text',
            'options' => [
                'label' => 'Complément textuel <span class="icon information" title="Complément en cas d\'échec de la recherche ci-dessus"> </span>:',
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],

            'attributes' => [
                'id' => 'text',
            ],
        ]);
        //Bouton
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

        //validateur S&S OR Text
    }
}