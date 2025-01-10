<?php

namespace toubeelib_rdv\core\repositoryInterfaces;

use toubeelib_rdv\core\domain\entities\praticien\Specialite;
use toubeelib_rdv\core\domain\entities\rdv\Rdv;

interface RdvRepositoryInterface
{
   public function getRdvById(string $id): Rdv;
   
   public function save(Rdv $rdv): string;

   public function getRdvByPatientId(string $id): array;

   public function update(Rdv $rdv): void;

   public function getRdvByPraticienId(string $id): array;
   
   public function getRdvPraticien(string $id, \DateTime $dateDebut, \DateTime $dateFin, string $idSpe, string $type): array;
}