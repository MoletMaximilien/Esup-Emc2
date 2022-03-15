-- STRUCTURE --------------------------------------------------------------------------------------------------

INSERT INTO unicaen_privilege_categorie (code, libelle, ordre, namespace)
    VALUES ('structure', 'Gestion des structures', 200, 'Structure\Provider\Privilege');

INSERT INTO unicaen_privilege_privilege(CATEGORIE_ID, CODE, LIBELLE, ORDRE)
WITH d(code, lib, ordre) AS (
    SELECT 'structure_index', 'Accéder à l''index des structures', 0 UNION
    SELECT 'structure_afficher', 'Afficher les structures', 10 UNION
    SELECT 'structure_description', 'Édition de la description', 20 UNION
    SELECT 'structure_gestionnaire', 'Gérer les gestionnaire', 30 UNION
    SELECT 'structure_complement_agent', 'Ajouter des compléments à propos des agents', 40
)
SELECT cp.id, d.code, d.lib, d.ordre
FROM d
JOIN unicaen_privilege_categorie cp ON cp.CODE = 'structure'
;

-- METIER -----------------------------------------------------------------------------------------------------

INSERT INTO unicaen_privilege_categorie (code, libelle, ordre, namespace)
    VALUES ('metier', 'Gestion des métiers', 551, 'Metier\Provider\Privilege');
INSERT INTO unicaen_privilege_privilege(CATEGORIE_ID, CODE, LIBELLE, ORDRE)
WITH d(code, lib, ordre) AS (
    SELECT 'metier_index', 'Afficher l''index des métiers', 10 UNION
    SELECT 'metier_afficher', 'Afficher un métier', 20 UNION
    SELECT 'metier_ajouter', 'Ajouter un métier', 30 UNION
    SELECT 'metier_modifier', 'Modifier un métier', 40 UNION
    SELECT 'metier_historiser', 'Historiser/Réstaurer un métier', 50 UNION
    SELECT 'metier_supprimer', 'Supprimer un métier', 60 UNION
    SELECT 'metier_cartographie', 'Extraire la cartographie', 100
)
SELECT cp.id, d.code, d.lib, d.ordre
FROM d
JOIN unicaen_privilege_categorie cp ON cp.CODE = 'metier';

INSERT INTO unicaen_privilege_categorie (code, libelle, ordre, namespace)
    VALUES ('domaine', 'Gestion des domaines', 552, 'Metier\Provider\Privilege');
INSERT INTO unicaen_privilege_privilege(CATEGORIE_ID, CODE, LIBELLE, ORDRE)
WITH d(code, lib, ordre) AS (
    SELECT 'domaine_index', 'Afficher l''index ', 10 UNION
    SELECT 'domaine_afficher', 'Afficher un domaine', 20 UNION
    SELECT 'domaine_ajouter', 'Ajouter un domaine', 30 UNION
    SELECT 'domaine_modifier', 'Modifier un domaine', 40 UNION
    SELECT 'domaine_historiser', 'Historiser/Restaurer un domaine', 50 UNION
    SELECT 'domaine_supprimer', 'Supprimer un domaine', 60
)
SELECT cp.id, d.code, d.lib, d.ordre
FROM d
JOIN unicaen_privilege_categorie cp ON cp.CODE = 'domaine';

INSERT INTO unicaen_privilege_categorie (code, libelle, ordre, namespace)
    VALUES ('familleProfessionnelle', 'Gestion des familles professionnelles ', 553, 'Metier\Provider\Privilege');
INSERT INTO unicaen_privilege_privilege(CATEGORIE_ID, CODE, LIBELLE, ORDRE)
WITH d(code, lib, ordre) AS (
    SELECT 'famille_professionnelle_index', 'Afficher l''index', 10 UNION
    SELECT 'famille_professionnelle_afficher', 'Afficher une famille professionnelle', 20 UNION
    SELECT 'famille_professionnelle_ajouter', 'Ajouter une famille professionnelle', 30 UNION
    SELECT 'famille_professionnelle_modifier', 'Modifier une famille professionnelle', 40 UNION
    SELECT 'famille_professionnelle_historiser', 'Historiser/Restaurer une famille professionnelle', 50 UNION
    SELECT 'famille_professionnelle_supprimer', 'Supprimer une famille professionnelle', 60
)
SELECT cp.id, d.code, d.lib, d.ordre
FROM d
JOIN unicaen_privilege_categorie cp ON cp.CODE = 'familleProfessionnelle';

INSERT INTO unicaen_privilege_categorie (code, libelle, ordre, namespace)
    VALUES ('referentielMetier', 'Gestion des référentiels métiers', 554, 'Metier\Provider\Privilege');
INSERT INTO unicaen_privilege_privilege(CATEGORIE_ID, CODE, LIBELLE, ORDRE)
WITH d(code, lib, ordre) AS (
    SELECT 'referentiel_index', 'Afficher l''index des référentiels métiers', 10 UNION
    SELECT 'referentiel_afficher', 'Afficher un référentiel métier', 20 UNION
    SELECT 'referentiel_ajouter', 'Ajouter un référentiel métier', 30 UNION
    SELECT 'referentiel_modifier', 'Modifier un référentiel métier', 40 UNION
    SELECT 'referentiel_historiser', 'Historiser/Restaurer un référentiel métier', 50 UNION
    SELECT 'referentiel_supprimer', 'Supprimer un référentiel métier', 60
)
SELECT cp.id, d.code, d.lib, d.ordre
FROM d
JOIN unicaen_privilege_categorie cp ON cp.CODE = 'referentielMetier';

INSERT INTO unicaen_privilege_categorie (code, libelle, ordre, namespace)
    VALUES ('referenceMetier', 'Gestion des références métiers', 555, 'Metier\Provider\Privilege');
INSERT INTO unicaen_privilege_privilege(CATEGORIE_ID, CODE, LIBELLE, ORDRE)
WITH d(code, lib, ordre) AS (
    SELECT 'reference_index', 'Afficher l''indes des références métiers', 10 UNION
    SELECT 'reference_afficher', 'Afficher une référence métier', 20 UNION
    SELECT 'reference_ajouter', 'Ajouter une référence métier', 30 UNION
    SELECT 'reference_modifier', 'Modifier une référence métier', 40 UNION
    SELECT 'reference_historiser', 'Historiser/Restaurer une référence métier', 50 UNION
    SELECT 'reference_supprimer', 'Supprimer une référence métier', 60
)
SELECT cp.id, d.code, d.lib, d.ordre
FROM d
JOIN unicaen_privilege_categorie cp ON cp.CODE = 'referenceMetier';

-- CARRIERE ---------------------------------------------------------------------------------------------------

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

-- ATTRIBUTION A L'ADMIN TECH ---------------------------------------------------------------------------------

TRUNCATE TABLE unicaen_privilege_privilege_role_linker;
INSERT INTO unicaen_privilege_privilege_role_linker (privilege_id, role_id)
WITH d(privilege_id) AS (
    SELECT id FROM unicaen_privilege_privilege
)
SELECT d.privilege_id, cp.id
FROM d
JOIN unicaen_utilisateur_role cp ON cp.role_id = 'Administrateur·trice technique'
;