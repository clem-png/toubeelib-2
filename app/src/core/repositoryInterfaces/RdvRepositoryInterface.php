<?php

namespace toubeelib\core\repositoryInterfaces;

use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\core\domain\entities\rdv\Rdv;

interface RdvRepositoryInterface
{
   public function getRdvById(string $id): Rdv;
   
   public function save(Rdv $rdv): string;

   public function getRdvByPatientId(string $id): array;

   public function update(Rdv $rdv): void;

   public function getRdvByPraticienId(string $id): array;
}