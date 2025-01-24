<?php

namespace toubeelib_rdv\core\services\authorisation;

interface AuthorisationServiceInterface
{
    public function authoriseAccesRDV(string $ressourceId, string $tokenId): bool;
    public function authorisePlanningAccess(string $tokenId, string $idPraticien): bool;
    public function authoriseCreateRDV(string $tokenId, ?string $idPatient, ?string $idPraticien): bool;
}