<?php

namespace Formation\Provider\Etat;

class DemandeExterneEtats {

    const TYPE = "DEMANDE_EXTERNE";

    const ETAT_CREATION_EN_COURS      = 'DEMANDE_EXTERNE_REDACTION';
    const ETAT_VALIDATION_AGENT       = 'DEMANDE_EXTERNE_AGENT';
    const ETAT_VALIDATION_RESP        = 'DEMANDE_EXTERNE_RESP';
    const ETAT_VALIDATION_DRH         = 'DEMANDE_EXTERNE_DRH';
    const ETAT_TERMINEE               = 'DEMANDE_EXTERNE_TERMINEE';
    const ETAT_REJETEE                = 'DEMANDE_EXTERNE_REJETEE';
}