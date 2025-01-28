\connect "patient";

INSERT INTO "patient" (id, num_secu, date_naissance, nom, prenom, adresse, mail) VALUES
    ('d4ac898d-0d47-48d6-b354-8282bec927ba', '1234567890123', '1990-01-01', 'Dupont', 'Jean', 'nancy', 'dupont.jean@mail.fr'),
    ('d8bfdec3-ca7c-4e72-98f2-749e5f775c86', '1234567890124', '1990-01-01', 'Durand', 'Pierre', 'vandeuve', 'durand.pierre@mail.fr'),
    ('326592e0-bf1f-4dda-8c51-7f4d6384491d', '1234567890125', '1990-01-01', 'Martin', 'Marie', '3lassou', 'martin.marie@mail.fr');

INSERT INTO num_patient ("idPatient", numero) VALUES
    ('d4ac898d-0d47-48d6-b354-8282bec927ba', '0644281281'),
    ('d8bfdec3-ca7c-4e72-98f2-749e5f775c86', '0644281282'),
    ('326592e0-bf1f-4dda-8c51-7f4d6384491d', '0644281283');

