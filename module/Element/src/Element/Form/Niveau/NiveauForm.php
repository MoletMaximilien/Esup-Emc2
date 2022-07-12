<?php

namespace Element\Form\Niveau;

use Application\Form\HasDescription\HasDescriptionFieldset;
use Element\Service\Niveau\NiveauServiceAwareTrait;
use Laminas\Form\Element\Button;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Number;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\Factory;
use Laminas\Validator\Callback;

class NiveauForm extends Form {
    use NiveauServiceAwareTrait;

    /** @var string type */
    private $type;

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return NiveauForm
     */
    public function setType(?string $type): NiveauForm
    {
        $this->type = $type;
        return $this;
    }


    public function init()
    {
        //libelle
        $this->add([
            'type' => Text::class,
            'name' => 'libelle',
            'options' => [
                'label' => "Libelle* :",
            ],
            'attributes' => [
                'id' => 'libelle',
            ],
        ]);
        //niveau
        $this->add([
            'type' => Number::class,
            'name' => 'niveau',
            'options' => [
                'label' => "Niveau * :",
            ],
            'attributes' => [
                'id' => 'niveau',
            ],
        ]);
        $this->add([
            'name' => 'old-niveau',
            'type' => Hidden::class,
            'attributes' => [
                'value' => "",
            ],
        ]);
        // description
        $this->add([
            'name' => 'HasDescription',
            'type' => HasDescriptionFieldset::class,
            'attributes' => [
                'id' => 'description',
            ]
        ]);
        //type
        $this->add([
            'type' => Text::class,
            'name' => 'type',
            'options' => [
                'label' => "Type * :",
            ],
            'attributes' => [
                'id' => 'type',
            ],
        ]);
        //submit
        $this->add([
            'type' => Button::class,
            'name' => 'creer',
            'options' => [
                'label' => '<i class="fas fa-save"></i> Enregistrer' ,
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
            'libelle'            => [ 'required' => true, ],
            'type'               => [ 'required' => true, ],
            'niveau'             => [
                'required' => true,
                'validators' => [[
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "Ce niveau est déjà utilisé",
                        ],
                        'callback' => function ($value, $context = []) {
                            if($value ==  $context['old-niveau']) return true;
                            return ($this->getNiveauService()->getMaitriseNiveauByNiveau($context['type'],$value) == null);
                        },
                    ],
                ]],
            ],
            'description'        => [ 'required' => false, ],
        ]));
    }
}