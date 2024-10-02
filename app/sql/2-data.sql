\connect "praticien";

INSERT INTO "praticien" ("id", "nom", "prenom", "adresse", "tel") VALUES
     ('cb771755-26f4-4e6c-b327-a1217f5b09cd',	'Dupont',	'Jean',	'nancy',	'0123456789'),
     ('8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c',	'Durand',	'Pierre',	'vandeuve',	'0123456789'),
     ('ce5b05aa-714e-486a-ae25-1bc6801403d1',	'Martin',	'Marie',	'3lassou',	'0123456789');

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

\connect "patient";

INSERT INTO "patient" (id, num_secu, date_naissance, nom, prenom, adresse, mail) VALUES
    ('d4ac898d-0d47-48d6-b354-8282bec927ba', '1234567890123', '1990-01-01', 'Dupont', 'Jean', 'nancy', 'dupont.jean@mail.fr'),
    ('d8bfdec3-ca7c-4e72-98f2-749e5f775c86', '1234567890124', '1990-01-01', 'Durand', 'Pierre', 'vandeuve', 'durand.pierre@mail.fr'),
    ('326592e0-bf1f-4dda-8c51-7f4d6384491d', '1234567890125', '1990-01-01', 'Martin', 'Marie', '3lassou', 'martin.marie@mail.fr');

INSERT INTO num_patient ("idPatient", numero) VALUES
    ('d4ac898d-0d47-48d6-b354-8282bec927ba', '1234567890123'),
    ('d8bfdec3-ca7c-4e72-98f2-749e5f775c86', '1234567890124'),
    ('326592e0-bf1f-4dda-8c51-7f4d6384491d', '1234567890125');

\connect "rdv";

INSERT INTO rdv (id, "idPraticien", "IdPatient", "idSpe", "dateDebut", status) VALUES
      ('89695d04-74eb-4b82-a0bd-b8b802803c57', 'cb771755-26f4-4e6c-b327-a1217f5b09cd', 'd4ac898d-0d47-48d6-b354-8282bec927ba', 'dd3cac4c-c175-427c-b2aa-8fcc54f250b5', '2021-01-01 08:00:00', 'prevu'),
      ('51d3ce98-4951-4ae0-827b-c436da776d33', '8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c', 'd8bfdec3-ca7c-4e72-98f2-749e5f775c86', '6542592f-8aaf-4ba5-9859-2169a686e9ae', '2021-01-01 08:00:00', 'prevu'),
      ('4ab01147-adca-4326-92e4-7e02bdab12f4', 'ce5b05aa-714e-486a-ae25-1bc6801403d1', '326592e0-bf1f-4dda-8c51-7f4d6384491d', '1d6f853e-f7fe-497f-abdd-7ee1430d14ed', '2021-01-01 08:00:00', 'prevu');