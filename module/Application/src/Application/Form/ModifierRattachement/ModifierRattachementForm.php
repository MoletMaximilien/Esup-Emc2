<?php

namespace Application\Form\ModifierRattachement;

use Application\Entity\Db\ParcoursDeFormation;
use Application\Service\Categorie\CategorieServiceAwareTrait;
use Application\Service\Metier\MetierServiceAwareTrait;
use Zend\Form\Element\Button;
use Zend\Form\Element\Select;
use Zend\Form\Form;
use Zend\InputFilter\Factory;
use Zend\Validator\Callback;

class ModifierRattachementForm extends Form
{
    use CategorieServiceAwareTrait;
    use MetierServiceAwareTrait;

    public function init()
    {
        $this->add([
            'type' => Select::class,
            'name' => 'type',
            'options' => [
                'label' => "Type de parcours de formations* :",
                'empty_option' => "Sélectionner un type de parcours  ...",
                'value_options' => [
                    ParcoursDeFormation::TYPE_CATEGORIE => ParcoursDeFormation::TYPE_CATEGORIE,
                    ParcoursDeFormation::TYPE_METIER => ParcoursDeFormation::TYPE_METIER,
                ],
            ],
            'attributes' => [
                'id' => 'type',
                'class'             => 'bootstrap-selectpicker show-tick',
                //'data-live-search'  => 'true',
            ],
        ]);

        $this->add([
            'type' => Select::class,
            'name' => 'categorie',
            'options' => [
                'label' => "Catégorie (laisser vide si concerne un métier) :",
                'empty_option' => "Sélectionner une catégorie ...",
                'value_options' => $this->getCategorieService()->getCategorieAsOption(),
            ],
            'attributes' => [
                'id' => 'categorie',
                'class'             => 'bootstrap-selectpicker show-tick',
                'data-live-search'  => 'true',
            ],
        ]);

        $this->add([
            'type' => Select::class,
            'name' => 'metier',
            'options' => [
                'label' => "Métier (laisser vide si concerne une catégorie) :",
                'empty_option' => "Sélectionner un métier ...",
                'value_options' => $this->getMetierService()->getMetiersTypesAsMultiOptions(),
            ],
            'attributes' => [
                'id' => 'metier',
                'class'             => 'bootstrap-selectpicker show-tick',
                'data-live-search'  => 'true',
            ],
        ]);

        //submit
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

        //inputFilter
        $this->setInputFilter((new Factory())->createInputFilter([
            'type' => [
                'required' => true,
                'validators' => [[
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "Le champ de référence (Catégorie ou métier) doit être saisi",
                        ],
                        'callback' => function ($value, $context = []) {
                            if($context['type'] === ParcoursDeFormation::TYPE_CATEGORIE) return $context['categorie'] !== '';
                            if($context['type'] === ParcoursDeFormation::TYPE_METIER) return $context['metier'] !== '';
                            return $value !== '';
                        },
                        //'break_chain_on_failure' => true,
                    ],
                ]],
            ],
            'metier'             => [ 'required' => false, ],
            'categorie'          => [ 'required' => false, ],
        ]));
    }
}