<?php

namespace toubeelib\core\services\rdv;

use toubeelib\core\services\rdv\RdvServiceException;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\RdvDTO;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\domain\entities\rdv\Rdv;

class ServiceRdv implements ServiceRDVInterface{

    private RdvRepositoryInterface $rdvRepository;

    public function __construct(RdvRepositoryInterface $rdvRepository){
        $this->rdvRepository = $rdvRepository;
    }

    public function consulterRdv(string $rdv_id){
        try {
            $rdv = $this->rdvRepository->getRdvById($rdv_id);
        }catch (\Exception $e){
            throw new RdvServiceException($e);
        }

        $rdvDTO = $rdv->toDTO();
        return $rdvDTO;
    }

    public function listerDisponibilitesPraticien(){
        // TODO: Implement listerDisponibilitesPraticien() method.
    }

    public function creerRdv(InputRdvDTO $DTO): RdvDTO{
        try{
            $rdv = New Rdv($DTO->idPatient,$DTO->idPraticien,$DTO->status,$DTO->dateDebut);
            $id = $this->rdvRepository->save($rdv);
            $rdv->setID($id);
        }catch (Exception $e){
            throw new RdvServiceException($e);
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