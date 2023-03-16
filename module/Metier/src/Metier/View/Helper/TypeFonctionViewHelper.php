<?php

namespace Metier\View\Helper;

use Laminas\View\Renderer\PhpRenderer;
use Metier\Entity\Db\Domaine;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\Partial;
use Laminas\View\Resolver\TemplatePathStack;

class TypeFonctionViewHelper extends AbstractHelper
{
    /**
     * @param Domaine $domaine
     * @param array $options
     * @return string|Partial
     */
    public function __invoke(Domaine $domaine, array $options = []) : string
    {
        /** @var PhpRenderer $view */
        $view = $this->getView();
        $view->resolver()->attach(new TemplatePathStack(['script_paths' => [__DIR__ . "/partial"]]));

        return $view->partial('type-fonction', ['domaine' => $domaine, 'options' => $options]);
    }
}