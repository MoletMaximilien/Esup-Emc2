create view V_PREECOG_AGENT as
SELECT DISTINCT (I.C_INDIVIDU_CHAINE) AS C_INDIVIDU,
                (I.prenom) AS PRENOM,
                (I.NOM_USAGE) AS NOM_USAGE
FROM OCTO.INDIVIDU I
LEFT JOIN INDIVIDU_AFFECTATION IA on I.C_INDIVIDU_CHAINE = IA.INDIVIDU_ID
WHERE
        I.NOM_USAGE IS NOT NULL
    AND (IA.DATE_DEBUT IS NOT NULL OR IA.DATE_DEBUT <= SYSDATE)
    AND (IA.DATE_FIN IS NULL OR IA.DATE_FIN >= SYSDATE)
    AND IA.TYPE_ID  = 1
    AND IA.STRUCTURE_ID = 1
/

create view V_PREECOG_AFFECTATION as
SELECT
       IA.ID AS AFFECTATION_ID,
       IA.INDIVIDU_ID AS AGENT_ID,
       S.ID AS STRUCTURE_ID,
       IA.DATE_DEBUT,
       IA.DATE_FIN,
       IA.ID_ORIG,
       IA.T_PRINCIPALE
FROM INDIVIDU_AFFECTATION IA
JOIN V_PREECOG_AGENT VPA ON VPA.C_INDIVIDU = IA.INDIVIDU_ID
JOIN STRUCTURE S ON IA.STRUCTURE_ID = S.ID
WHERE
    VPA.C_INDIVIDU IS NOT NULL
    AND IA.TYPE_ID = 2
/

create view V_PREECOG_GRADE as
SELECT
    IG."ID",IG."ID_ORIG",IG."INDIVIDU_ID" AS AGENT_ID,IG."STRUCTURE_ID",IG."CORPS_ID",IG."GRADE_ID",IG."BAP_ID",IG."EMPLOITYPE_ID",IG."CNU_ID",IG."CNU_SPECIALITE_ID",IG."DISCIPLINE_SEC_ID",IG."D_DEBUT",IG."D_FIN",IG."INM1",IG."INM2"
FROM INDIVIDU_GRADE IG
JOIN V_PREECOG_AGENT VPA ON VPA.C_INDIVIDU = IG.INDIVIDU_ID
JOIN STRUCTURE S ON IG.STRUCTURE_ID = S.ID
/

create view V_PREECOG_STATUT as
SELECT
    IST."ID",IST."ID_ORIG",IST."C_SOURCE",IST."INDIVIDU_ID" as AGENT_ID,IST."STRUCTURE_ID",IST."D_DEBUT",IST."D_FIN",IST."T_TITULAIRE",IST."T_CDI",IST."T_CDD",IST."T_VACATAIRE",IST."T_ENSEIGNANT",IST."T_ADMINISTRATIF",IST."T_CHERCHEUR",IST."T_ETUDIANT",IST."T_AUDITEUR_LIBRE",IST."T_DOCTORANT",IST."T_DETACHE_IN",IST."T_DETACHE_OUT",IST."T_DISPO",IST."T_HEBERGE",IST."T_EMERITE",IST."T_RETRAITE",IST."T_CLD",IST."T_CLM"
FROM INDIVIDU_STATUT IST
JOIN V_PREECOG_AGENT VPA ON VPA.C_INDIVIDU = IST.INDIVIDU_ID
JOIN STRUCTURE S ON IST.STRUCTURE_ID = S.ID
/

