-- Adminer 4.8.1 PostgreSQL 16.4 (Debian 16.4-1.pgdg120+1) dump

create database patient;
create database praticien;
create database rdv;
create database users;

\connect "patient";

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

DROP TABLE IF EXISTS "patient";
CREATE TABLE "public"."patient" (
                                    "id" uuid DEFAULT uuid_generate_v4() NOT NULL,
                                    "num_secu" character varying,
                                    "date_naissance" date,
                                    "nom" character varying,
                                    "prenom" character varying,
                                    "adresse" character varying,
                                    "mail" character varying
) WITH (oids = false);

TRUNCATE "patient";

DROP TABLE IF EXISTS "num_patient";
CREATE TABLE "public"."num_patient" (
                                        "idPatient" uuid NOT NULL,
                                        "numero" character varying NOT NULL
) WITH (oids = false);

TRUNCATE "num_patient";

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

\connect "rdv";

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

DROP TABLE IF EXISTS "rdv";
CREATE TABLE "public"."rdv" (
                                "id" uuid DEFAULT uuid_generate_v4() NOT NULL,
                                "idPraticien" uuid NOT NULL,
                                "IdPatient" uuid NOT NULL,
                                "idSpe" uuid NOT NULL,
                                "dateDebut" timestamp NOT NULL,
                                "status" character varying NOT NULL
) WITH (oids = false);

TRUNCATE "rdv";

\connect "users";

DROP TABLE IF EXISTS "users";
CREATE TABLE "public"."users" (
                                  "id" uuid NOT NULL,
                                  "email" character varying(128) NOT NULL,
                                  "password" character varying(256) NOT NULL,
                                  "role" smallint DEFAULT '0' NOT NULL,
                                  CONSTRAINT "users_email" UNIQUE ("email"),
                                  CONSTRAINT "users_id" PRIMARY KEY ("id")
) WITH (oids = false);

TRUNCATE "users";

-- 2024-10-01 15:47:39.813346+00