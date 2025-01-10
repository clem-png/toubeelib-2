\connect "praticien";

INSERT INTO "praticien" ("id", "nom", "prenom", "adresse", "tel") VALUES
    ('cb771755-26f4-4e6c-b327-a1217f5b09cd',	'Dupont',	'Jean',	'nancy',	'0123456789'),
    ('8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c',	'Durand',	'Pierre',	'vandeuve',	'0123456788'),
    ('ce5b05aa-714e-486a-ae25-1bc6801403d1',	'Martin',	'Marie',	'3lassou',	'0123456787');

INSERT INTO "specialite" ("id", "label", "desc") VALUES
    ('dd3cac4c-c175-427c-b2aa-8fcc54f250b5', 'Dentiste', 'Spécialiste des dents'),
    ('6542592f-8aaf-4ba5-9859-2169a686e9ae', 'Ophtalmolgue', 'Spécialiste des yeux'),
    ('1d6f853e-f7fe-497f-abdd-7ee1430d14ed', 'Généraliste', 'Médecin généraliste'),
    ('0691944a-166e-460b-93fe-4df51ea5cd46', 'Pédiatre', 'Médecin pour enfants'),
    ('bfb23d48-5cee-4627-acc3-09491d15015e', 'Médecin du sport', 'Maladies et trausmatismes liés à la pratique sportive');

INSERT INTO "praticien_spe" ("idPraticien", "idSpe") VALUES
    ('cb771755-26f4-4e6c-b327-a1217f5b09cd', 'dd3cac4c-c175-427c-b2aa-8fcc54f250b5'),
    ('8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c', '6542592f-8aaf-4ba5-9859-2169a686e9ae'),
    ('ce5b05aa-714e-486a-ae25-1bc6801403d1', '1d6f853e-f7fe-497f-abdd-7ee1430d14ed');
