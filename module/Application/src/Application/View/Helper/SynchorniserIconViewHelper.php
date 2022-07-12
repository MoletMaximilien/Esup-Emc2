<?php

namespace Application\View\Helper;

use Application\View\Renderer\PhpRenderer;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\Partial;
use Laminas\View\Resolver\TemplatePathStack;

class SynchorniserIconViewHelper extends AbstractHelper
{

    /**
     * @param bool|null $infos
     * @param array $options
     * @return string|Partial
     */
    public function __invoke(?bool $infos = true, $options = [])
    {
        /** @var PhpRenderer $view */
        $view = $this->getView();
        $view->resolver()->attach(new TemplatePathStack(['script_paths' => [__DIR__ . "/partial"]]));

        return $view->partial('synchroniser-icon', ['infos' => $infos, 'options' => $options]);
    }
}