<?php

namespace toubeelib\core\services\rdv;

use DateTime;
use Exception;
use toubeelib\core\services\rdv\RdvServiceException;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\RdvDTO;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\domain\entities\rdv\Rdv;
use toubeelib\core\services\praticien\ServicePraticienInterface;


class ServiceRdv implements ServiceRDVInterface{

    private RdvRepositoryInterface $rdvRepository;
    private ServicePraticienInterface $praticienService;

    const JOURS_CONSULTATION = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    const HEURE_DEBUT = '09:00';
    const HEURE_FIN = '17:00';
    const DUREE_RDV = '30'; //minutes

    public function __construct(RdvRepositoryInterface $rdvRepository, ServicePraticienInterface $praticienService){
        $this->rdvRepository = $rdvRepository;
        $this->praticienService = $praticienService;
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

    public function listerDisponibilitePraticien(DateTime $dateDebut, DateTime $dateFin, string $id): array {

        try {
            $praticien = $this->praticienService->getPraticienById($id);
            if (!$praticien) {
                throw new RdvServiceException("Practitioner not found");
            }
            if ($dateDebut > $dateFin) {
                throw new RdvServiceException("Invalid date range");
            }
        } catch (\Exception $e) {
            throw new RdvServiceException($e);
        }

        $disponibilites = [];
    
        for ($date = clone $dateDebut; $date <= $dateFin; $date->modify('+1 day')) {
            if (in_array($date->format('l'), self::JOURS_CONSULTATION)) {
                $heureDebut = DateTime::createFromFormat('H:i', self::HEURE_DEBUT);
                $heureFin = DateTime::createFromFormat('H:i', self::HEURE_FIN);

                while ($heureDebut < $heureFin) {
                    $disponibilites[] = (clone $date)->setTime((int)$heureDebut->format('H'), (int)$heureDebut->format('i'));
                    $heureDebut->modify('+' . self::DUREE_RDV . ' minutes');
                }
            }
        }

        $rdvs = $this->rdvRepository->getRdvByPraticienId($id);

        foreach ($rdvs as $rdv) {
            $key = array_search($rdv->dateDebut, $disponibilites);
            if ($key !== false) {
                unset($disponibilites[$key]);
            }
        }
    
        return array_values($disponibilites);
    }

    public function creerRdv(InputRdvDTO $DTO): RdvDTO{
        try{
            // Vérifier si le praticien existe
            $praticien = $this->praticienService->getPraticienById($DTO->idPraticien);
            if (!$praticien) {
                throw new RdvServiceException("Practitioner not found");
            }

            // TODO: Verifier les spécialités du praticien et les disponibilités

            $rdv = new Rdv($DTO->idPraticien, $DTO->idPatient, $DTO->status, $DTO->dateDebut);
            $id = $this->rdvRepository->save($rdv);
            $rdv->setID($id);
        } catch (Exception $e){
            throw new RdvServiceException($e);
        }
        return new RdvDTO($rdv);
    }

    public function annulerRdv(string $rdv_id):void{
        try {
            $rdv = $this->rdvRepository->getRdvById($rdv_id);
            if ($rdv->status === 'Cancelled') {
                throw new RdvServiceException('Rdv already cancelled');
            }
            $rdv->setStatus("Cancelled");
            $this->rdvRepository->update($rdv);
        } catch (\Exception $e){
            throw new RdvServiceException($e);
        }
    }

    public function modifierPatientRdv(String $rdv_id, String $patient_id):void{
        try{
            $rdv = $this->rdvRepository->getRdvById($rdv_id);
            $rdv->setPatientId($patient_id);
            $this->rdvRepository->update($rdv);
        }catch (\Exception $e){
            throw new RdvServiceException($e);
        }
    }

    public function modifierSpecialiteRdv(){
        //TODO : Implement modifierSpecialiteRdv() method.
    }
}