-- Date de MAJ 23/11/2023 ----------------------------------------------------------------------------------------------
-- Script avant version 4.1.1 ------------------------------------------------------------------------------------------
-- Color scheme : orange  ----------------------------------------------------------------------------------------------


-- TTTTTTTTTTTTTTTTTTTTTTT         AAA               BBBBBBBBBBBBBBBBB   LLLLLLLLLLL             EEEEEEEEEEEEEEEEEEEEEE
-- T:::::::::::::::::::::T        A:::A              B::::::::::::::::B  L:::::::::L             E::::::::::::::::::::E
-- T:::::::::::::::::::::T       A:::::A             B::::::BBBBBB:::::B L:::::::::L             E::::::::::::::::::::E
-- T:::::TT:::::::TT:::::T      A:::::::A            BB:::::B     B:::::BLL:::::::LL             EE::::::EEEEEEEEE::::E
-- TTTTTT  T:::::T  TTTTTT     A:::::::::A             B::::B     B:::::B  L:::::L                 E:::::E       EEEEEE
--         T:::::T            A:::::A:::::A            B::::B     B:::::B  L:::::L                 E:::::E
--         T:::::T           A:::::A A:::::A           B::::BBBBBB:::::B   L:::::L                 E::::::EEEEEEEEEE
--         T:::::T          A:::::A   A:::::A          B:::::::::::::BB    L:::::L                 E:::::::::::::::E
--         T:::::T         A:::::A     A:::::A         B::::BBBBBB:::::B   L:::::L                 E:::::::::::::::E
--         T:::::T        A:::::AAAAAAAAA:::::A        B::::B     B:::::B  L:::::L                 E::::::EEEEEEEEEE
--         T:::::T       A:::::::::::::::::::::A       B::::B     B:::::B  L:::::L                 E:::::E
--         T:::::T      A:::::AAAAAAAAAAAAA:::::A      B::::B     B:::::B  L:::::L         LLLLLL  E:::::E       EEEEEE
--       TT:::::::TT   A:::::A             A:::::A   BB:::::BBBBBB::::::BLL:::::::LLLLLLLLL:::::LEE::::::EEEEEEEE:::::E
--       T:::::::::T  A:::::A               A:::::A  B:::::::::::::::::B L::::::::::::::::::::::LE::::::::::::::::::::E
--       T:::::::::T A:::::A                 A:::::A B::::::::::::::::B  L::::::::::::::::::::::LE::::::::::::::::::::E
--       TTTTTTTTTTTAAAAAAA                   AAAAAAABBBBBBBBBBBBBBBBB   LLLLLLLLLLLLLLLLLLLLLLLLEEEEEEEEEEEEEEEEEEEEEE

-- ---------------------------------------------------------------------------------------------------------------------
-- Entité de base ------------------------------------------------------------------------------------------------------
-- ---------------------------------------------------------------------------------------------------------------------

create table structure_type
(
    id                    bigint                                                             not null
        constraint structure_type_pk
            primary key,
    code                  varchar(64)                                                        not null,
    libelle               varchar(256)                                                       not null,
    description           text,
    source_id             varchar(128),
    id_orig               varchar(100),
    created_on            timestamp(0) default ('now'::text)::timestamp(0) without time zone not null,
    updated_on            timestamp(0),
    deleted_on            timestamp(0),
    histo_createur_id     bigint,
    histo_modificateur_id bigint,
    histo_destructeur_id  bigint
);

create table structure
(
    id                    bigint                                                             not null
        constraint structure_pk
            primary key,
    code                  varchar(40),
    sigle                 varchar(40),
    libelle_court         varchar(128),
    libelle_long          varchar(1024),
    type_id               bigint
        constraint structure_structure_type_id_fk
            references structure_type
            on delete set null,
    d_ouverture           timestamp,
    d_fermeture           timestamp,
    fermeture_ow          timestamp,
    resume_mere           boolean      default false,
    description           text,
    adresse_fonctionnelle varchar(1024),
    parent_id             bigint,
    niv2_id               bigint,
    niv2_id_ow            bigint,
    source_id             varchar(128),
    id_orig               varchar(100),
    created_on            timestamp(0) default ('now'::text)::timestamp(0) without time zone not null,
    updated_on            timestamp(0),
    deleted_on            timestamp(0),
    histo_createur_id     bigint,
    histo_modificateur_id bigint,
    histo_destructeur_id  bigint
);

-- ---------------------------------------------------------------------------------------------------------------------
-- TABLE - Responsabilité ----------------------------------------------------------------------------------------------
-- ---------------------------------------------------------------------------------------------------------------------

create table structure_responsable
(
    id                    bigserial
        constraint structure_responsable_pk
        primary key,
    structure_id          integer                                                            not null,
    agent_id              varchar(40)                                                        not null,
    fonction_id           integer,
    date_debut            timestamp,
    date_fin              timestamp,
    source_id             varchar(128),
    id_orig               varchar(100),
    created_on            timestamp(0) default ('now'::text)::timestamp(0) without time zone not null,
    updated_on            timestamp(0),
    deleted_on            timestamp(0),
    histo_createur_id     bigint       default 0,
    histo_modificateur_id bigint,
    histo_destructeur_id  bigint
);

create table structure_gestionnaire
(
    id                    bigserial
        constraint structure_gestionnaire_pk
        primary key,
    structure_id          integer                                                            not null,
    agent_id              varchar(40)                                                        not null,
    fonction_id           integer,
    date_debut            timestamp,
    date_fin              timestamp,
    source_id             varchar(128),
    id_orig               varchar(100),
    created_on            timestamp(0) default ('now'::text)::timestamp(0) without time zone not null,
    updated_on            timestamp(0),
    deleted_on            timestamp(0),
    histo_createur_id     bigint       default 0,
    histo_modificateur_id bigint,
    histo_destructeur_id  bigint
);

create table structure_observateur
(
    id                    serial
        constraint structure_observateur_pk
            primary key,
    structure_id          integer                 not null
        constraint structure_observateur_structure_id_fk
            references structure
            on delete cascade,
    utilisateur_id        integer                 not null
        constraint structure_observateur_unicaen_utilisateur_user_id_fk
            references unicaen_utilisateur_user
            on delete cascade,
    description           text,
    histo_creation        timestamp default now() not null,
    histo_createur_id     integer   default 0     not null
        constraint structure_observateur_unicaen_utilisateur_user_id_fk_2
            references unicaen_utilisateur_user,
    histo_modification    timestamp,
    histo_modificateur_id integer
        constraint structure_observateur_unicaen_utilisateur_user_id_fk_3
            references unicaen_utilisateur_user,
    histo_destruction     timestamp,
    histo_destructeur_id  integer
        constraint structure_observateur_unicaen_utilisateur_user_id_fk_4
            references unicaen_utilisateur_user
);
-- ---------------------------------------------------------------------------------------------------------------------
-- TABLE - Forcage -----------------------------------------------------------------------------------------------------
-- ---------------------------------------------------------------------------------------------------------------------

create table structure_agent_force
(
    id                    serial
        constraint structure_agent_force_pk
            primary key,
    structure_id          integer     not null,
    agent_id              varchar(40) not null
        constraint structure_agent_force_agent_c_individu_fk
            references agent,
    histo_creation        timestamp   not null,
    histo_createur_id     integer     not null
        constraint structure_agent_force_unicaen_utilisateur_user_id_fk
            references unicaen_utilisateur_user,
    histo_modification    timestamp   not null,
    histo_modificateur_id integer     not null
        constraint structure_agent_force_unicaen_utilisateur_user_id_fk_2
            references unicaen_utilisateur_user,
    histo_destruction     timestamp,
    histo_destructeur_id  integer
        constraint structure_agent_force_unicaen_utilisateur_user_id_fk_3
            references unicaen_utilisateur_user
);
create unique index structure_agent_force_id_uindex on structure_agent_force (id);


-- TODO retirer car fonctionnalité supprimée
-- create table structure_ficheposte
-- (
--     structure_id  integer not null,
--     ficheposte_id integer not null
--         constraint structure_ficheposte_fiche_poste_id_fk
--             references ficheposte
--             on delete cascade,
--     constraint structure_ficheposte_pk
--         primary key (structure_id, ficheposte_id)
-- );


-- IIIIIIIIIINNNNNNNN        NNNNNNNN   SSSSSSSSSSSSSSS EEEEEEEEEEEEEEEEEEEEEERRRRRRRRRRRRRRRRR   TTTTTTTTTTTTTTTTTTTTTTT
-- I::::::::IN:::::::N       N::::::N SS:::::::::::::::SE::::::::::::::::::::ER::::::::::::::::R  T:::::::::::::::::::::T
-- I::::::::IN::::::::N      N::::::NS:::::SSSSSS::::::SE::::::::::::::::::::ER::::::RRRRRR:::::R T:::::::::::::::::::::T
-- II::::::IIN:::::::::N     N::::::NS:::::S     SSSSSSSEE::::::EEEEEEEEE::::ERR:::::R     R:::::RT:::::TT:::::::TT:::::T
--   I::::I  N::::::::::N    N::::::NS:::::S              E:::::E       EEEEEE  R::::R     R:::::RTTTTTT  T:::::T  TTTTTT
--   I::::I  N:::::::::::N   N::::::NS:::::S              E:::::E               R::::R     R:::::R        T:::::T
--   I::::I  N:::::::N::::N  N::::::N S::::SSSS           E::::::EEEEEEEEEE     R::::RRRRRR:::::R         T:::::T
--   I::::I  N::::::N N::::N N::::::N  SS::::::SSSSS      E:::::::::::::::E     R:::::::::::::RR          T:::::T
--   I::::I  N::::::N  N::::N:::::::N    SSS::::::::SS    E:::::::::::::::E     R::::RRRRRR:::::R         T:::::T
--   I::::I  N::::::N   N:::::::::::N       SSSSSS::::S   E::::::EEEEEEEEEE     R::::R     R:::::R        T:::::T
--   I::::I  N::::::N    N::::::::::N            S:::::S  E:::::E               R::::R     R:::::R        T:::::T
--   I::::I  N::::::N     N:::::::::N            S:::::S  E:::::E       EEEEEE  R::::R     R:::::R        T:::::T
-- II::::::IIN::::::N      N::::::::NSSSSSSS     S:::::SEE::::::EEEEEEEE:::::ERR:::::R     R:::::R      TT:::::::TT
-- I::::::::IN::::::N       N:::::::NS::::::SSSSSS:::::SE::::::::::::::::::::ER::::::R     R:::::R      T:::::::::T
-- I::::::::IN::::::N        N::::::NS:::::::::::::::SS E::::::::::::::::::::ER::::::R     R:::::R      T:::::::::T
-- IIIIIIIIIINNNNNNNN         NNNNNNN SSSSSSSSSSSSSSS   EEEEEEEEEEEEEEEEEEEEEERRRRRRRR     RRRRRRR      TTTTTTTTTTT


-- ---------------------------------------------------------------------------------------------------------------------
-- ROLE ----------------------------------------------------------------------------------------------------------------
-- ---------------------------------------------------------------------------------------------------------------------

INSERT INTO unicaen_utilisateur_role (role_id, libelle, is_default, is_auto, accessible_exterieur, description) VALUES
    ('Responsable de structure', 'Responsable de structure', false, true, true, null),
    ('Gestionnaire de structure', 'Gestionnaire de structure', false, true, true, null),
    ('Observateur·trice de la structure', 'Observateur·trice (Structure)', false, true, true, 'Observateur·trice limité·e au périmètre d''une structrure')
;

-- ---------------------------------------------------------------------------------------------------------------------
-- PARAMETRE -----------------------------------------------------------------------------------------------------------
-- ---------------------------------------------------------------------------------------------------------------------

INSERT INTO unicaen_parametre_categorie (code, libelle, ordre, description)
VALUES ('STRUCTURE', 'Paramètres liés aux structures', 1000, null);
INSERT INTO unicaen_parametre_parametre(CATEGORIE_ID, CODE, LIBELLE, DESCRIPTION, VALEURS_POSSIBLES, VALEUR, ORDRE)
WITH d(CODE, LIBELLE, DESCRIPTION, VALEURS_POSSIBLES, VALEUR, ORDRE) AS (
    SELECT 'AGENT_TEMOIN_STATUT', 'Filtre sur les témoins de statuts associés aux agents affiché·es dans la partie structure', 'Il s''agit d''une cha&icirc;ne de caract&egrave;res reli&eacute;e par des ; avec les temoins suivant : cdi, cdd, titulaire, vacataire, enseignant, administratif, chercheur, doctorant, detacheIn, detacheOut, dispo <br/> Le modificateur ! est une n&eacute;gation.</p>', 'String', 'administratif;!dispo;!doctorant', 100 UNION
    SELECT 'AGENT_TEMOIN_AFFECTATION', 'Filtre sur les témoins d''affectations associés aux agents affiché·es dans la partie structure', 'Il s''agit d''une cha&icirc;ne de caract&egrave;res reli&eacute;e par des ; avec les temoins suivant : principale, hierarchique, fonctionnelle <br/> Le modificateur ! est une n&eacute;gation.</p>', 'String', 'principale', 200 UNION
    SELECT 'AGENT_TEMOIN_EMPLOITYPE', 'Filtres associés aux emploi-types', null, 'String', null, 300 UNION
    SELECT 'AGENT_TEMOIN_GRADE', 'Filtres associés aux grades', 'Filtrage basé sur les libellés courts (p.e. "IGE CN")', 'String', null, 400 UNION
    SELECT 'AGENT_TEMOIN_CORPS', 'Filtres associés aux corps', 'Filtrage basé sur les libellés courts (p.e. "ASI RF")', 'String', null, 500 UNION
    SELECT 'BLOC_OBSERVATEUR', 'Affichage du bloc - Observateur·trices -', null, 'Boolean', null, 10 UNION
    SELECT 'BLOC_GESTIONNAIRE', 'Affichage du bloc - Gestionnaires -', null, 'Boolean', null, 11
)
SELECT cp.id, d.CODE, d.LIBELLE, d.DESCRIPTION, d.VALEURS_POSSIBLES, d.VALEUR, d.ORDRE
FROM d
JOIN unicaen_parametre_categorie cp ON cp.CODE = 'STRUCTURE';

-- VALEUR RECOMMANDEE
update unicaen_parametre_parametre set valeur='false' where code='BLOC_OBSERVATEUR';
update unicaen_parametre_parametre set valeur='false' where code='BLOC_GESTIONNAIRE';

-- ---------------------------------------------------------------------------------------------------------------------
-- PRIVILEGES ----------------------------------------------------------------------------------------------------------
-- ---------------------------------------------------------------------------------------------------------------------

INSERT INTO unicaen_privilege_categorie (code, libelle, namespace, ordre)
VALUES ('structure', 'Gestion des structures', 'Structure\Provider\Privilege', 100);
INSERT INTO unicaen_privilege_privilege(CATEGORIE_ID, CODE, LIBELLE, ORDRE)
WITH d(code, lib, ordre) AS (
    SELECT 'structure_index', 'Accéder à l''index des structures', 0 UNION
    SELECT 'structure_afficher', 'Afficher les structures', 10 UNION
    SELECT 'structure_description', 'Édition de la description', 20 UNION
    SELECT 'structure_gestionnaire', 'Gérer les gestionnaire', 30 UNION
    SELECT 'structure_complement_agent', 'Ajouter des compléments à propos des agents', 40 UNION
    SELECT 'structure_agent_force', 'Ajouter/Retirer des agents manuellements', 50 UNION
    SELECT 'structure_agent_masque', 'Afficher/Masquer les agents exclus de la structure', 60
)
SELECT cp.id, d.code, d.lib, d.ordre
FROM d
JOIN unicaen_privilege_categorie cp ON cp.CODE = 'structure';


INSERT INTO unicaen_privilege_categorie (code, libelle, namespace, ordre)
VALUES ('structureobservateur', 'Gestion des observateur·trice de structure', 'Structure\Provider\Privilege', 200);
INSERT INTO unicaen_privilege_privilege(CATEGORIE_ID, CODE, LIBELLE, ORDRE)
WITH d(code, lib, ordre) AS (
    SELECT 'structureobservateur_index', 'Accéder à l''index', 10 UNION
    SELECT 'structureobservateur_afficher', 'Afficher', 20 UNION
    SELECT 'structureobservateur_ajouter', 'Ajouter', 30 UNION
    SELECT 'structureobservateur_modifier', 'Modifier', 40 UNION
    SELECT 'structureobservateur_historiser', 'Historiser/Restaurer', 50 UNION
    SELECT 'structureobservateur_supprimer', 'Supprimer', 60 UNION
    SELECT 'structureobservateur_indexobservateur', 'Index - Les structures dont vous êtes observateur·trice - ', 100
)
SELECT cp.id, d.code, d.lib, d.ordre
FROM d
JOIN unicaen_privilege_categorie cp ON cp.CODE = 'structureobservateur';

-- ---------------------------------------------------------------------------------------------------------------------
-- MACROS ASSOCIEES ----------------------------------------------------------------------------------------------------
-- ---------------------------------------------------------------------------------------------------------------------

INSERT INTO unicaen_renderer_macro (code, description, variable_name, methode_name) VALUES
('STRUCTURE#bloc', null, 'structure', 'toStringStructureBloc'),
('STRUCTURE#gestionnaires', '<p>Affiche sous la forme d''un listing les Gestionnaires de la structure</p>', 'structure', 'toStringGestionnaires'),
('STRUCTURE#libelle', '<p>Retourne le libellé de la structure</p>', 'structure', 'toStringLibelle'),
('STRUCTURE#libelle_long', '<p>Retourne le libellé de la structure + le libell&amp;eacute de la structure de niveau 2</p>', 'structure', 'toStringLibelleLong'),
('STRUCTURE#responsables', '<p>Affiches sous la forme d''un listing les Responsables d''une structure</p>', 'structure', 'toStringResponsables'),
('STRUCTURE#resume', null, 'structure', 'toStringResume');


