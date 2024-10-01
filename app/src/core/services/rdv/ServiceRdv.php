<?php

namespace toubeelib\core\services\rdv;

use DateTime;
use Exception;
use Monolog\Level;
use Psr\Log\LoggerInterface;
use toubeelib\core\services\rdv\RdvServiceException;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\RdvDTO;
use toubeelib\core\dto\SpecialiteDTO;
use toubeelib\core\dto\InputSpecialiteDTO;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\domain\entities\rdv\Rdv;
use toubeelib\core\services\praticien\ServicePraticienInterface;
use toubeelib\core\domain\entities\praticien\Specialite;


class ServiceRdv implements ServiceRDVInterface{

    private RdvRepositoryInterface $rdvRepository;
    private ServicePraticienInterface $praticienService;

    private LoggerInterface $logger;

    const JOURS_CONSULTATION = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    const HEURE_DEBUT = '09:00';
    const HEURE_FIN = '17:00';
    const DUREE_RDV = '30'; //minutes

    public function __construct(RdvRepositoryInterface $rdvRepository, ServicePraticienInterface $praticienService, LoggerInterface $logger){
        $this->rdvRepository = $rdvRepository;
        $this->praticienService = $praticienService;
        $this->logger = $logger;
    }

    /**
     * @throws RdvServiceException
     */
    public function consulterRdv(string $rdv_id): RdvDTO
    {
        try {
            $rdv = $this->rdvRepository->getRdvById($rdv_id);
        }catch (Exception $e){
            throw new RdvServiceException($e);
        }

        return $rdv->toDTO();
    }

    /**
     * @throws \DateMalformedStringException
     * @throws RdvServiceException
     */
    public function listerDisponibilitePraticien(DateTime $dateDebut, DateTime $dateFin, string $id): array {

        try {
            $praticien = $this->praticienService->getPraticienById($id);
            if ($dateDebut > $dateFin) {
                throw new RdvServiceException("Invalid date range");
            }
        } catch (Exception $e) {
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

    /**
     * @throws RdvServiceException
     */
    public function creerRdv(InputRdvDTO $DTO): RdvDTO{
        try{
            // Vérifier si le praticien existe
            $praticien = $this->praticienService->getPraticienById($DTO->idPraticien);

            //vérifier si la spécialité est la même que celle du praticien
            if($DTO->specialite !== null){
                $specialite = $this->praticienService->getSpecialiteById($DTO->specialite->id);
                if (!$specialite) {
                    throw new RdvServiceException("Specialite pas trouvé");
                }

                if ($specialite->label !== $praticien->specialite_label) {
                    throw new RdvServiceException("Specialite non valide");
                }
            }

            // Vérifier si le créneau est disponible
            $date = DateTime::createFromImmutable($DTO->dateDebut);
            $dateFin = (clone $date)->modify('+' . self::DUREE_RDV . ' minutes'); 
            $disponibilites = $this->listerDisponibilitePraticien($date, $dateFin, $DTO->idPraticien);
            if (!in_array($date, $disponibilites)) {
                throw new RdvServiceException("Créneau non disponible");
            }

            $rdv = new Rdv($DTO->idPraticien, $DTO->idPatient, $DTO->status, $DTO->dateDebut);

            if($DTO->specialite !== null){
                $specialite = $this->praticienService->getSpecialiteById($DTO->specialite->id);
                $rdv->setSpecialite(new Specialite($specialite->ID, $specialite->label, $specialite->description));
            }

            $id = $this->rdvRepository->save($rdv);
            $rdv->setID($id);
            $this->logger->log(Level::Info, "Creation RDV : " . $id);
        } catch (Exception $e){
            throw new RdvServiceException($e);
        }
        return new RdvDTO($rdv);
    }

    /**
     * @throws RdvServiceException
     */
    public function annulerRdv(string $rdv_id):RdvDTO{
        try {
            $rdv = $this->rdvRepository->getRdvById($rdv_id);
            if ($rdv->status === 'Cancelled') {
                throw new RdvServiceException('Rdv already cancelled');
            }
            $rdv->setStatus("Cancelled");
            $this->rdvRepository->update($rdv);
            $this->logger->log(Level::Info, "Annulation RDV : " . $rdv_id);
            return new RdvDTO($rdv);
        } catch (Exception $e){
            throw new RdvServiceException($e);
        }
    }

    /**
     * @throws RdvServiceException
     */
    public function modifierPatientOuSpecialiteRdv(string $rdv_id, ?string $patient_id = null, ?InputSpecialiteDTO $specialiteInput = null): RdvDTO {
        try {
            $logAction = "";
            $rdv = $this->rdvRepository->getRdvById($rdv_id);
            if ($patient_id !== null) {
                $rdv->setPatientId($patient_id);
                $logAction.= " Nouveau Patient : ".$patient_id;
            }
            if ($specialiteInput !== null) {
                $specialite = $this->praticienService->getSpecialiteById($specialiteInput->id);
                $rdv->setSpecialite(new Specialite($specialite->ID, $specialite->label, $specialite->description));
                $logAction.= " Nouvelle spécialité  : ".$specialite->label;
            }
            $this->rdvRepository->update($rdv);
            $this->logger->log(Level::Info, "Modification RDV : " . $rdv_id . " -".$logAction);
            return new RdvDTO($rdv);
        } catch (Exception $e) {
            throw new RdvServiceException($e);
        }
    }
}