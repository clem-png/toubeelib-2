<?php

namespace toubeelib_rdv\core\services\authorisation;

use toubeelib_rdv\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib_rdv\core\services\authorisation\AuthorisationServiceInterface;
use toubeelib_rdv\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class AuthorisationService implements AuthorisationServiceInterface
{
    private RdvRepositoryInterface $repository;

    public function __construct(RdvRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function authoriseAccesRDV(string $ressourceId, string $tokenId): bool{
        try{
            $rdv = $this->repository->getRdvById($ressourceId);
        }catch(RepositoryEntityNotFoundException $e){
            return false;
        }

        if($rdv->idPatient === $tokenId){
            return true;
        }

        if($rdv->idPraticien === $tokenId){
            return true;
        }

        return false;
    }

    public function authorisePlanningAccess(string $tokenId, string $idPraticien): bool{
        if($idPraticien === $tokenId){
            return true;
        }

        return false;
    }

    public function authoriseCreateRDV(string $tokenId, ?string $idPatient, ?string $idPraticien): bool{

        if($idPatient === $tokenId){
            return true;
        }

        if($idPraticien === $tokenId){
            return true;
        }

        return false;
    }
}