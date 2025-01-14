-- Adminer 4.8.1 PostgreSQL 16.4 (Debian 16.4-1.pgdg120+1) dump

create database users;

\connect "users";

-- Enable the uuid-ossp extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

DROP TABLE IF EXISTS "users";
CREATE TABLE "public"."users" (
                                  "id" uuid NOT NULL DEFAULT uuid_generate_v4(),
                                  "email" character varying(128) NOT NULL,
                                  "password" character varying(256) NOT NULL,
                                  "role" smallint DEFAULT '0' NOT NULL,
                                  CONSTRAINT "users_email" UNIQUE ("email"),
                                  CONSTRAINT "users_id" PRIMARY KEY ("id")
) WITH (oids = false);

TRUNCATE "users";

-- 2024-10-01 15:47:39.813346+00