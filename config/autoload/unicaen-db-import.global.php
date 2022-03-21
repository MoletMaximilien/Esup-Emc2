<?php

namespace Application;

use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'import' => [
        'connections' => [
            // Format: 'identifiant unique' => 'nom de la connexion Doctrine'
            'default' => 'doctrine.connection.orm_default',
            'octopus' => 'doctrine.connection.orm_octopus',
        ],

        'synchros' => [
            [
                'name' => 'Import_AGENT',
                'source' => [
                    'name'               => 'Agents geres par la DRH',
                    'select'             => 'SELECT * FROM v_agent',
                    'connection'         => 'octopus',
                    'source_code_column' => 'C_INDIVIDU',
                    'columns'            => [],
                ],
                'destination' => [
                    'name'               => 'Agents gérés par la DRH',
                    'table'              => 'agent',
                    'connection'         => 'default',
                    'source_code_column' => 'c_individu',
                    'columns'            => [],
                ],
            ],
            [
                'name' => 'Import_BAP',
                'source' => [
                    'name'               => 'BAP des agents remonté depuis OCTOPUS',
                    'select'             => 'SELECT id, code as c_bap, lib_court, lib_long, d_fermeture AS histo FROM correspondance',
                    'connection'         => 'octopus',
                    'source_code_column' => 'ID',
                    'columns'            => ['c_bap', 'lib_court', 'lib_long', 'histo'],
                ],
                'intermediate_table' => 'src_correspondance',
                'destination' => [
                    'name'               => 'BAP des agents remonté depuis OCTOPUS',
                    'table'              => 'carriere_correspondance',
                    'connection'         => 'default',
                    'source_code_column' => 'id',
                    'columns'            => ['c_bap', 'lib_court', 'lib_long', 'histo'],
                ],
            ],
            [
                'name' => 'Import_CORPS',
                'source' => [
                    'name'               => 'CORPS des agents remonté depuis OCTOPUS',
                    'select'             => 'SELECT id, code, lib_court, lib_long, categorie_code as categorie, d_fermeture AS histo FROM corps',
                    'connection'         => 'octopus',
                    'source_code_column' => 'ID',
                    'columns'            => ['code', 'lib_court', 'lib_long', 'categorie', 'histo'],
                ],
                'intermediate_table' => 'src_corps',
                'destination' => [
                    'name'               => 'CORPS des agents remonté depuis OCTOPUS',
                    'table'              => 'carriere_corps',
                    'connection'         => 'default',
                    'source_code_column' => 'id',
                    'columns'            => ['code', 'lib_court', 'lib_long', 'categorie', 'histo'],
                ],
            ],
            [
                'name' => 'Import_GRADE',
                'source' => [
                    'name'               => 'GRADE des agents remontés depuis OCTOPUS',
                    'select'             => 'SELECT id, code, lib_court, lib_long, d_fermeture AS histo FROM grade',
                    'connection'         => 'octopus',
                    'source_code_column' => 'ID',
                    'columns'            => ['code', 'lib_court', 'lib_long', 'histo'],
                ],
                'intermediate_table' => 'src_grade',
                'destination' => [
                    'name'               => 'GRADE des agents remontés depuis OCTOPUS',
                    'table'              => 'carriere_grade',
                    'connection'         => 'default',
                    'source_code_column' => 'id',
                    'columns'            => ['code', 'lib_court', 'lib_long', 'histo'],
                ],
            ],
            [
                'name' => 'Import_STRUCTURE_TYPE',
                'source' => [
                    'name'               => 'Type des structures remonté depuis OCTOPUS',
                    'select'             => 'SELECT ID, CODE, LIBELLE FROM STRUCTURE_TYPE',
                    'connection'         => 'octopus',
                    'source_code_column' => 'ID',
                    'columns'            => ['CODE', 'LIBELLE'],
                ],
                'intermediate_table' => 'src_structure_type',
                'destination' => [
                    'name'               => 'Type des structures remonté depuis OCTOPUS',
                    'table'              => 'structure_type',
                    'connection'         => 'default',
                    'source_code_column' => 'id',
                    'columns'            => ['code', 'libelle'],
                ],
            ],
            [
                'name' => 'Import_STRUCTURE',
                'source' => [
                    'name'               => 'Agents geres par la DRH',
                    'select'             => 'SELECT ID, CODE, LIBELLE_COURT, LIBELLE_LONG, TYPE_ID, DATE_OUVERTURE AS OUVERTURE, DATE_FERMETURE AS FERMETURE, PARENT_ID, NIV2_ID FROM STRUCTURE',
                    'connection'         => 'octopus',
                    'source_code_column' => 'ID',
                ],
                'intermediate_table' => 'src_structure',
                'destination' => [
                    'name'               => 'Structure stockees dans octopus',
                    'table'              => 'structure',
                    'connection'         => 'default',
                    'source_code_column' => 'id',
                    'columns'            => ['code', 'libelle_court', 'libelle_long', 'type_id', 'ouverture', 'fermeture', 'parent_id', 'niv2_id'],
                ],
            ],
            [
                'name' => 'Import_AGENT_AFFECTATION',
                'source' => [
                    'name'               => 'Affectations des agents',
                    'select'             => 'SELECT INDIVIDU_AFFECTATION.ID AS AFFECTATION_ID, INDIVIDU_AFFECTATION.individu_id as agent_id, INDIVIDU_AFFECTATION.structure_id, INDIVIDU_AFFECTATION.date_debut, INDIVIDU_AFFECTATION.date_fin, INDIVIDU_AFFECTATION.id_orig, INDIVIDU_AFFECTATION.t_principale FROM INDIVIDU_AFFECTATION JOIN V_AGENT on V_AGENT.C_INDIVIDU = INDIVIDU_AFFECTATION.INDIVIDU_ID',
                    'connection'         => 'octopus',
                    'source_code_column' => 'affectation_id',
                    'columns'            => ['agent_id', 'STRUCTURE_ID', 'DATE_DEBUT', 'DATE_FIN', 'ID_ORIG', 'T_PRINCIPALE'],
                ],
                'destination' => [
                    'name'               => 'Affectation des agents gérés par la DRH',
                    'table'              => 'agent_carriere_affectation',
                    'connection'         => 'default',
                    'source_code_column' => 'affectation_id',
                    'columns'            => ['agent_id', 'structure_id', 'date_debut', 'date_fin', 'id_orig', 't_principale'],
                ],
            ],
            [
                'name' => 'Import_AGENT_GRADE',
                'source' => [
                    'name'               => 'Grades liés aux agents de PreeCog',
                    'select'             => 'SELECT ID, ID_ORIG, AGENT_ID, STRUCTURE_ID, CORPS_ID, GRADE_ID, CORRESPONDANCE_ID AS BAP_ID, D_DEBUT, D_FIN FROM INDIVIDU_GRADE',
                    'connection'         => 'octopus',
                    'source_code_column' => 'ID',
                    //'columns'            => ['AGENT_ID', 'STRUCTURE_ID', 'CORPS_ID', 'GRADE_ID', 'BAP_ID', 'DATE_DEBUT', 'DATE_FIN'],
                ],
                'intermediate_table' => 'src_agent_grade',
                'destination' => [
                    'name'               => 'Grade des agents gérés par la DRH',
                    'table'              => 'agent_carriere_grade',
                    'connection'         => 'default',
                    'source_code_column' => 'id',
                    'columns'            => ['id_orig', 'agent_id', 'structure_id', 'corps_id', 'grade_id', 'bap_id', 'd_debut', 'd_fin'],
                ],
            ],
            [
                'name' => 'Import_AGENT_STATUT',
                'source' => [
                    'name'               => 'Statut des agents geres par la DRH',
                    'select'             => 'SELECT ID, ID_ORIG, C_SOURCE, INDIVIDU_ID AS AGENT_ID, STRUCTURE_ID, D_DEBUT, D_FIN, T_TITULAIRE, T_CDI, T_CDD, T_VACATAIRE, T_ENSEIGNANT, T_ADMINISTRATIF, T_CHERCHEUR, T_ETUDIANT, T_AUDITEUR_LIBRE, T_DOCTORANT, T_DETACHE_IN, T_DETACHE_OUT, T_DISPO, T_HEBERGE, T_EMERITE, T_RETRAITE    FROM INDIVIDU_STATUT',
                    'connection'         => 'octopus',
                    'source_code_column' => 'ID',
//                    'columns'            => ['ID_ORIG', 'C_SOURCE', 'INDIVIDU_ID', 'STRUCTURE_ID', 'D_DEBUT', 'D_FIN', 'T_TITULAIRE', 'T_CDI', 'T_CDD', 'T_VACATAIRE', 'T_ENSEIGNANT', 'T_ADMINISTRATIF', 'T_CHERCHEUR', 'T_ETUDIANT', 'T_AUDITEUR_LIBRE', 'T_DOCTORANT', 'T_DETACHE_IN', 'T_DETACHE_OUT', 'T_DISPO', 'T_HEBERGE', 'T_EMERITE', 'T_RETRAITE', 'T_CLD', 'T_CLM'],
                ],
                'intermediate_table' => 'src_agent_statut',
                'destination' => [
                    'name'               => 'Statut des agents geres par la DRH',
                    'table'              => 'agent_carriere_statut',
                    'connection'         => 'default',
                    'source_code_column' => 'id',
                    'columns'            => ['id_orig', 'c_source', 'individu_id', 'structure_id', 'd_debut', 'd_fin', 't_titulaire', 't_cdi', 't_cdd', 't_vacataire', 't_enseignant', 't_administratif', 't_chercheur', 't_etudiant', 't_auditeur_libre', 't_doctorant', 't_detache_in', 't_detache_out', 't_dispo', 't_heberge', 't_emerite', 't_retraite', 't_cld', 't_clm'],
                    'columns_to_char' => [
                        'd_debut' => "TO_CHAR(%s,'YYYY-MM-DD')",
                        'd_fin'   => "TO_CHAR(%s,'YYYY-MM-DD')",
                    ],
                ],
            ],
            [
                'name' => 'Import_AGENT_QUOTITE',
                'source' => [
                    'name'               => 'Quotité travaillé par les agents',
                    'select'             => 'SELECT ID, INDIVIDU_ID AS AGENT_ID, DEBUT AS DEBUT, FIN AS FIN, QUOTITE FROM INDIVIDU_QUOTITE JOIN V_AGENT on V_AGENT.C_INDIVIDU = INDIVIDU_QUOTITE.INDIVIDU_ID',
                    'connection'         => 'octopus',
                    'source_code_column' => 'ID',
                ],
                'intermediate_table' => 'src_agent_quotite',
                'destination' => [
                    'name'               => 'Grade des agents gérés par la DRH',
                    'table'              => 'agent_carriere_quotite',
                    'connection'         => 'default',
                    'source_code_column' => 'id',
                    'columns'            => ['agent_id', 'debut', 'fin', 'quotite'],
                ],
            ],
            [
                'name' => 'Import_STRUCTURE_RESPONSABLE',
                'source' => [
                    'name'               => 'Responsables des structures',
                    'select'             => 'SELECT ID, STRUCTURE_ID, INDIVIDU_ID AS AGENT_ID, FONCTION_ID, DATE_DEBUT, DATE_FIN FROM STRUCTURE_RESPONSABLE',
                    'connection'         => 'octopus',
                    'source_code_column' => 'ID',
//                    'columns'            => ['STRUCTURE_ID', 'AGENT_ID', 'FONCTION_ID', 'DATE_DEBUT', 'DATE_FIN'],
                ],
                'destination' => [
                    'name'               => 'Responsables des strucures',
                    'table'              => 'structure_responsable',
                    'connection'         => 'default',
                    'source_code_column' => 'id',
                    'columns'            => ['structure_id', 'agent_id', 'fonction_id', 'date_debut', 'date_fin'],
                ],
            ],
            [
                'name' => 'Import_STRUCTURE_GESTIONNAIRE',
                'source' => [
                    'name'               => 'Responsables des structures',
                    'select'             => 'SELECT ID, STRUCTURE_ID, INDIVIDU_ID AS AGENT_ID, FONCTION_ID, DATE_DEBUT, DATE_FIN FROM STRUCTURE_GESTIONNAIRE',
                    'connection'         => 'octopus',
                    'source_code_column' => 'ID',
//                    'columns'            => ['STRUCTURE_ID', 'AGENT_ID', 'FONCTION_ID', 'DATE_DEBUT', 'DATE_FIN'],
                ],
                'destination' => [
                    'name'               => 'Gestionnaire des strucures',
                    'table'              => 'structure_gestionnaire',
                    'connection'         => 'default',
                    'source_code_column' => 'id',
                    'columns'            => ['structure_id', 'agent_id', 'fonction_id', 'date_debut', 'date_fin'],
                ],
            ],

        ],
    ],

    'bjyauthorize'    => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => 'UnicaenDbImport\Controller\Console',
                    'action'     => [
                        'runImport',
                        'runSynchro',
                    ],
                    'roles' => [],
                ],
            ],
        ],
    ],

];
