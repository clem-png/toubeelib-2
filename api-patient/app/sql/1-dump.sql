-- Adminer 4.8.1 PostgreSQL 16.4 (Debian 16.4-1.pgdg120+1) dump

create database patient;

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
