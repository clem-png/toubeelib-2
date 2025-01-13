-- Adminer 4.8.1 PostgreSQL 16.4 (Debian 16.4-1.pgdg120+1) dump

create database rdv;

\connect "rdv";

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

DROP TABLE IF EXISTS "rdv";
CREATE TABLE "public"."rdv" (
                                "id" uuid DEFAULT uuid_generate_v4() NOT NULL,
                                "idPraticien" uuid NOT NULL,
                                "IdPatient" uuid NOT NULL,
                                "idSpe" uuid NULL,
                                "dateDebut" timestamp NOT NULL,
                                "status" character varying NOT NULL,
                                "type" character varying NOT NULL
) WITH (oids = false);

TRUNCATE "rdv";

-- 2024-10-01 15:47:39.813346+00