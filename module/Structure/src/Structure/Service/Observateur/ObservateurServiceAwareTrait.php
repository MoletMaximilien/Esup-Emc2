<?php

namespace Structure\Service\Observateur;

trait ObservateurServiceAwareTrait
{
    protected ObservateurService $observateurStructureService;

    public function getObservateurService(): ObservateurService
    {
        return $this->observateurStructureService;
    }

    public function setObservateurService(ObservateurService $observateurStructureService): void
    {
        $this->observateurStructureService = $observateurStructureService;
    }


}