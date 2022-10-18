create table carriere_niveau
(
    id serial                                       constraint niveau_definition_pk primary key,
    niveau                integer       not null,
    libelle               varchar(1024) not null,
    description           text,
    label                 varchar(64)   not null,
    histo_creation        timestamp     not null,
    histo_createur_id     integer       not null    constraint niveau_definition_unicaen_utilisateur_user_id_fk references unicaen_utilisateur_user,
    histo_modification    timestamp,
    histo_modificateur_id integer                   constraint niveau_definition_unicaen_utilisateur_user_id_fk_2 references unicaen_utilisateur_user,
    histo_destruction     timestamp,
    histo_destructeur_id  integer                   constraint niveau_definition_unicaen_utilisateur_user_id_fk_3 references unicaen_utilisateur_user
);
create unique index niveau_definition_id_uindex on carriere_niveau (id);

create table carriere_niveau_enveloppe
(
    id serial                                       constraint niveau_enveloppe_pk primary key,
    borne_inferieure_id   integer   not null        constraint niveau_enveloppe_niveau_definition_id_fk references carriere_niveau,
    borne_superieure_id   integer   not null        constraint niveau_enveloppe_niveau_definition_id_fk_2 references carriere_niveau,
    valeur_recommandee_id integer                   constraint niveau_enveloppe_niveau_definition_id_fk_3 references carriere_niveau on delete set null,
    description           text,
    histo_creation        timestamp not null,
    histo_createur_id     integer   not null        constraint niveau_enveloppe_unicaen_utilisateur_user_id_fk references unicaen_utilisateur_user,
    histo_modification    timestamp,
    histo_modificateur_id integer                   constraint niveau_enveloppe_unicaen_utilisateur_user_id_fk_2 references unicaen_utilisateur_user,
    histo_destruction     integer,
    histo_destructeur_id  integer                   constraint niveau_enveloppe_unicaen_utilisateur_user_id_fk_3 references unicaen_utilisateur_user
);
create unique index niveau_enveloppe_id_uindex on carriere_niveau_enveloppe (id);

create table carriere_categorie
(
    id serial                                       constraint categorie_pk primary key,
    code                  varchar(255)  not null,
    libelle               varchar(1024) not null,
    histo_creation        timestamp     not null,
    histo_createur_id     integer       not null    constraint categorie_user_id_fk references unicaen_utilisateur_user,
    histo_modification    timestamp     ,
    histo_modificateur_id integer                   constraint categorie_user_id_fk_2 references unicaen_utilisateur_user,
    histo_destruction     timestamp,
    histo_destructeur_id  integer                   constraint categorie_user_id_fk_3 references unicaen_utilisateur_user
);
create unique index categorie_code_uindex on carriere_categorie (code);
create unique index categorie_id_uindex on carriere_categorie (id);

create table carriere_correspondance
(
    id                    bigint not null unique constraint correspondance_pk primary key,
    c_bap                 varchar(10),
    lib_court             varchar(20),
    lib_long              varchar(200),
    d_ouverture           timestamp,
    d_fermeture           timestamp,
    source_id             bigint,
    id_orig               varchar(100),
    created_on            timestamp(0) default ('now'::text)::timestamp(0) without time zone not null,
    updated_on            timestamp(0),
    deleted_on            timestamp(0),
    histo_createur_id     bigint,
    histo_modificateur_id bigint,
    histo_destructeur_id  bigint
);

create table carriere_corps
(
    id                    bigint not null unique constraint corps_pk primary key,
    lib_court             varchar(20),
    lib_long              varchar(200),
    code                  varchar(10) not null,
    categorie             varchar(10),
    niveau                integer,
    niveaux_id            integer constraint carriere_corps_carriere_niveau_enveloppe_id_fk references carriere_niveau_enveloppe on delete set null,
    d_ouverture           timestamp,
    d_fermeture           timestamp,
    source_id             bigint,
    id_orig               varchar(100),
    created_on            timestamp(0) default ('now'::text)::timestamp(0) without time zone not null,
    updated_on            timestamp(0),
    deleted_on            timestamp(0),
    histo_createur_id     bigint,
    histo_modificateur_id bigint,
    histo_destructeur_id  bigint
);

create table carriere_grade
(
    id                    bigint not null unique constraint grade_pk primary key,
    lib_court             varchar(20),
    lib_long              varchar(200),
    code                  varchar(20) not null,
    d_ouverture           timestamp,
    d_fermeture           timestamp,
    source_id             bigint,
    id_orig               varchar(100),
    created_on            timestamp(0) default ('now'::text)::timestamp(0) without time zone not null,
    updated_on            timestamp(0),
    deleted_on            timestamp(0),
    histo_createur_id     bigint,
    histo_modificateur_id bigint,
    histo_destructeur_id  bigint
);


-------------------------------------------------------------------------------------------------------------
-- DATA -----------------------------------------------------------------------------------------------------
-------------------------------------------------------------------------------------------------------------

INSERT INTO carriere_categorie (code, libelle, histo_creation, histo_createur_id) VALUES ('A', 'La catégorie A', now(), 0);
INSERT INTO carriere_categorie (code, libelle, histo_creation, histo_createur_id) VALUES ('B', 'La catégorie B', now(), 0);
INSERT INTO carriere_categorie (code, libelle, histo_creation, histo_createur_id) VALUES ('C', 'La catégorie C', now(), 0);


--------------------------------------------------------------------------------------------------------------
-- PARAMS ----------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------

INSERT INTO unicaen_parametre_categorie (code, libelle, ordre) VALUES ('CARRIERE', 'Paramètre du module Carrière', 2);
INSERT INTO unicaen_parametre_parametre(CATEGORIE_ID, CODE, LIBELLE, DESCRIPTION, VALEURS_POSSIBLES, VALEUR, ORDRE)
WITH d(CODE, LIBELLE, DESCRIPTION, VALEURS_POSSIBLES, VALEUR, ORDRE) AS (
    SELECT 'CorpsAvecAgent', 'Affichage seulement des corps avec agent', null, 'Boolean', 'true', 100 UNION
    SELECT 'GradeAvecAgent', 'Affichage seulement des grades avec agent', null, 'Boolean', 'true', 200 UNION
    SELECT 'CorrespondanceAvecAgent', 'Affichage seulement des correspondance ayant un agent', null, 'Boolean', 'true', 30
)
SELECT cp.id, d.CODE, d.LIBELLE, d.DESCRIPTION, d.VALEURS_POSSIBLES, d.VALEUR, d.ORDRE
FROM d
JOIN unicaen_parametre_categorie cp ON cp.CODE = 'CARRIERE';


--------------------------------------------------------------------------------------------------------------
-- PRIVILEGE -------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------

INSERT INTO unicaen_privilege_categorie (code, libelle, ordre, namespace)
VALUES ('categorie', 'Gestion des catégories (carrière)', 600, 'Carriere\Provider\Privilege');
INSERT INTO unicaen_privilege_privilege(CATEGORIE_ID, CODE, LIBELLE, ORDRE)
WITH d(code, lib, ordre) AS (
    SELECT 'categorie_index', 'Accéder à l''index des catégories', 10 UNION
    SELECT 'categorie_afficher', 'Afficher une catégorie', 20 UNION
    SELECT 'categorie_modifier', 'Modifier une catégorie', 30
)
SELECT cp.id, d.code, d.lib, d.ordre
FROM d
JOIN unicaen_privilege_categorie cp ON cp.CODE = 'categorie';

INSERT INTO unicaen_privilege_categorie (code, libelle, ordre, namespace)
VALUES ('corps', 'Gestion des corps', 610, 'Carriere\Provider\Privilege');
INSERT INTO unicaen_privilege_privilege(CATEGORIE_ID, CODE, LIBELLE, ORDRE)
WITH d(code, lib, ordre) AS (
    SELECT 'corps_index', 'Accéder à l''index des corps', 10 UNION
    SELECT 'corps_afficher', 'Afficher les corps', 20 UNION
    SELECT 'corps_modifier', 'Modifier un corps', 30
)
SELECT cp.id, d.code, d.lib, d.ordre
FROM d
JOIN unicaen_privilege_categorie cp ON cp.CODE = 'corps';

INSERT INTO unicaen_privilege_categorie (code, libelle, ordre, namespace)
VALUES ('grade', 'Gestion des grades', 620, 'Carriere\Provider\Privilege');
INSERT INTO unicaen_privilege_privilege(CATEGORIE_ID, CODE, LIBELLE, ORDRE)
WITH d(code, lib, ordre) AS (
    SELECT 'grade_index', 'Accéder à l''index des grades', 10 UNION
    SELECT 'grade_afficher', 'Afficher un grade', 20 UNION
    SELECT 'grade_modifier', 'Modifier un grade', 30
)
SELECT cp.id, d.code, d.lib, d.ordre
FROM d
JOIN unicaen_privilege_categorie cp ON cp.CODE = 'grade';

INSERT INTO unicaen_privilege_categorie (code, libelle, ordre, namespace)
VALUES ('correspondance', 'Gestion des correspondances', 630, 'Carriere\Provider\Privilege');
INSERT INTO unicaen_privilege_privilege(CATEGORIE_ID, CODE, LIBELLE, ORDRE)
WITH d(code, lib, ordre) AS (
    SELECT 'correspondance_index', 'Accéder à l''index des correspondances', 10 UNION
    SELECT 'correspondance_afficher', 'Afficher une correspondance', 20 UNION
    SELECT 'correspondance_modifier', 'Modifier une correspondance', 30
)
SELECT cp.id, d.code, d.lib, d.ordre
FROM d
         JOIN unicaen_privilege_categorie cp ON cp.CODE = 'correspondance';