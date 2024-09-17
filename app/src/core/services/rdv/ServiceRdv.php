<?php

namespace toubeelib\core\services\rdv;

use PharIo\Manifest\Exception;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\RdvDTO;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;

class ServiceRdv implements ServiceRDVInterface{

    private RdvRepositoryInterface $rdvRepository;

    public function __construct(RdvRepositoryInterface $rdvRepository){
        $this->rdvRepository = $rdvRepository;
    }

    public function consulterRdv(string $rdv_id){
        try {
            $rdv = $this->rdvRepository->getRdvById($rdv_id);
        }catch (Exception $e){
            throw new \Exception($e);
        }

        $rdvDTO = $rdv->toDTO();
        // TODO : FAIRE LES EXCEPTIONS ET VERIFICATION
        return $rdvDTO;
    }

    public function listerDisponibilitesPraticien(){
        // TODO: Implement listerDisponibilitesPraticien() method.
    }

    public function creerRdv(InputRdvDTO $DTO): RdvDTO{
        try{
            $rdv = New Rdv($DTO->get("idPatient"),$DTO->get("idPraticien"),$DTO->get("dateDebut"),$DTO->get("status"));
        }catch (Exception $e){
            throw new \Exception($e);
        }
        return new RdvDTO($rdv);
    }

    public function annulerRdv(){
        // TODO: Implement annulerRdv() method.
    }

    public function modifierPatientRdv(){
        // TODO: Implement modifierPatientRdv() method.
    }
}