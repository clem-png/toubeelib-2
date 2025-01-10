-- Adminer 4.8.1 PostgreSQL 16.4 (Debian 16.4-1.pgdg120+1) dump

create database praticien;

\connect "praticien";

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

DROP TABLE IF EXISTS "praticien";
CREATE TABLE "public"."praticien" (
                                      "id" uuid DEFAULT uuid_generate_v4() NOT NULL,
                                      "nom" character varying,
                                      "prenom" character varying,
                                      "adresse" character varying,
                                      "tel" character varying
) WITH (oids = false);

TRUNCATE "praticien";


DROP TABLE IF EXISTS "praticien_spe";
CREATE TABLE "public"."praticien_spe" (
                                          "idPraticien" uuid NOT NULL,
                                          "idSpe" uuid NOT NULL
) WITH (oids = false);

TRUNCATE "praticien_spe";

DROP TABLE IF EXISTS "specialite";
CREATE TABLE "public"."specialite" (
                                       "id" uuid DEFAULT uuid_generate_v4() NOT NULL,
                                       "label" character varying,
                                       "desc" text
) WITH (oids = false);

TRUNCATE "specialite";

DROP TABLE IF EXISTS "disponibilite";
CREATE TABLE "public"."disponibilite" (
                                          "id" uuid DEFAULT uuid_generate_v4() NOT NULL,
                                          "idPraticien" uuid NOT NULL,
                                          "jour" integer NOT NULL,
                                          "heureDebut" time NOT NULL,
                                          "heureFin" time NOT NULL,
                                          "dureeRdv" integer NOT NULL
) WITH (oids = false);

TRUNCATE "disponibilite";

-- 2024-10-01 15:47:39.813346+00