<?php

namespace toubeelib_rdv\core\services\rdv;

use DateTime;
use Exception;
use Monolog\Level;
use Psr\Log\LoggerInterface;
use toubeelib_rdv\core\services\rdv\RdvServiceException;
use toubeelib_rdv\core\dto\InputRdvDTO;
use toubeelib_rdv\core\dto\RdvDTO;
use toubeelib_rdv\core\dto\SpecialiteDTO;
use toubeelib_rdv\core\dto\InputSpecialiteDTO;
use toubeelib_rdv\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib_rdv\core\domain\entities\rdv\Rdv;
use toubeelib_rdv\core\services\praticien\ServicePraticienInterface;
use toubeelib_rdv\core\domain\entities\praticien\Specialite;
use toubeelib_rdv\core\dto\PatientDTO;
use toubeelib_rdv\core\dto\PraticienDTO;



class ServiceRdv implements ServiceRDVInterface{

    private RdvRepositoryInterface $rdvRepository;
    private ServicePraticienInterface $praticienService;
    private ServicePatientInterface $patientService;

    private LoggerInterface $logger;

    const JOURS_CONSULTATION = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    const HEURE_DEBUT = '07:00';
    const HEURE_FIN = '17:00';
    const DUREE_RDV = '30'; //minutes

    public function __construct(RdvRepositoryInterface $rdvRepository, ServicePraticienInterface $praticienService, ServicePatientInterface $patientService, LoggerInterface $logger){
        $this->rdvRepository = $rdvRepository;
        $this->praticienService = $praticienService;
        $this->patientService = $patientService;
        $this->logger = $logger;
    }

    // Création et modification des RDV
     
    public function creerRdv(InputRdvDTO $DTO): RdvDTO {
        try {
            // Vérifier si le praticien existe
            $praticien = $this->praticienService->getPraticienById($DTO->idPraticien);
            if (!$praticien) {
                throw new RdvServiceException("Praticien pas trouvé");
            }
    
            // Vérifier si la spécialité est la même que celle du praticien
            if ($DTO->specialite !== null) {
                $specialite = $this->praticienService->getSpecialiteById($DTO->specialite->id);
                if (!$specialite) {
                    throw new RdvServiceException("Specialite pas trouvé");
                }
    
                if ($specialite->label !== $praticien->specialite) {
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
    
            $rdv = new Rdv($DTO->idPraticien, $DTO->idPatient, "prevu", $DTO->dateDebut, $DTO->type);
    
            if ($DTO->specialite !== null) {
                $specialite = $this->praticienService->getSpecialiteById($DTO->specialite->id);
                if ($specialite === null) {
                    throw new RdvServiceException("Specialite pas trouvé lors de la création du RDV");
                }
                if ($specialite->ID === null) {
                    throw new RdvServiceException("Specialite ID est null lors de la création du RDV");
                }
                $rdv->setSpecialite(new Specialite($specialite->ID, $specialite->label, $specialite->description));
            }
    
            $id = $this->rdvRepository->save($rdv);
            $rdv->setID($id);
            $this->logger->log(Level::Info, "Creation RDV : " . $id);
        } catch (Exception $e) {
            throw new RdvServiceException($e);
        }
        return new RdvDTO($rdv);
    }

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
        } catch (\Exception $e) {
            throw new RdvServiceException($e);
        }
    }
    
    public function indisponibilitePraticien(DateTime $dateDebut, DateTime $dateFin, string $id): void {
        try {
            $praticien = $this->praticienService->getPraticienById($id);
            if (!$praticien) {
                throw new RdvServiceException("Praticien pas trouvé");
            }
    
            if ($dateDebut > $dateFin) {
                throw new RdvServiceException("Plage de dates invalide");
            }
    
            for ($date = clone $dateDebut; $date <= $dateFin; $date->modify('+1 day')) {
                if (in_array($date->format('l'), self::JOURS_CONSULTATION)) {
                    $heureDebut = DateTime::createFromFormat('H:i', self::HEURE_DEBUT);
                    $heureFin = DateTime::createFromFormat('H:i', self::HEURE_FIN);
    
                    while ($heureDebut < $heureFin) {
                        $rdv = new Rdv($id, null, "indisponible", (clone $date)->setTime((int)$heureDebut->format('H'), (int)$heureDebut->format('i')), null);
                        $this->rdvRepository->save($rdv);
                        $heureDebut->modify('+' . self::DUREE_RDV . ' minutes');
                    }
                }
            }
    
            $this->logger->log(Level::Info, "Indisponibilité créée pour le praticien : " . $id . " du " . $dateDebut->format('Y-m-d H:i') . " au " . $dateFin->format('Y-m-d H:i'));
        } catch (Exception $e) {
            throw new RdvServiceException($e);
        }
    }
    
    // Consultations sur les RDV

    public function consulterRdv(string $rdv_id): RdvDTO{
        try {
            $rdv = $this->rdvRepository->getRdvById($rdv_id);
        }catch (\Exception $e){
            throw new RdvServiceException($e);
        }

        try {
            $specialite = $this->praticienService->getSpecialiteById($rdv->specialite->ID);
            if(!$specialite){
                throw new RdvServiceException("Specialite not found");
            }
            $rdv->setSpecialite(new Specialite($specialite->ID, $specialite->label, $specialite->description));
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

    public function listerRdvPatient(string $patient_id): array {
        try {
            $rdvs = $this->rdvRepository->getRdvByPatientId($patient_id);
            $rdvsDTO = [];
            foreach ($rdvs as $rdv) {
                $rdvsDTO[] = new RdvDTO($rdv);
            }
            return $rdvsDTO;
        } catch (\Exception $e) {
            throw new RdvServiceException($e);
        }
    }

    public function afficherPlanningPraticien(DateTime $dateDebut, DateTime $dateFin, string $id, string $idSpe, string $type): array{
        try {
            $praticien = $this->praticienService->getPraticienById($id);
            if (!$praticien) {
            throw new RdvServiceException("Praticien pas trouvé");
            }

            $specialite = $this->praticienService->getSpecialiteById($idSpe);
            if (!$specialite) {
            throw new RdvServiceException("Specialité pas trouvée");
            }

            if ($dateDebut > $dateFin) {
            throw new RdvServiceException("Plage de dates invalide");
            }

            $rdvs = $this->rdvRepository->getRdvPraticien($id, $dateDebut, $dateFin, $idSpe, $type);
            $rdvsDTO = [];
            foreach ($rdvs as $rdv) {
                $rdvDTO = new RdvDTO($rdv);
                $rdvDTO->setSpecialiteLabel($specialite->label);
                $rdvsDTO[] = $rdvDTO;
            }
            return $rdvsDTO;
        } catch (\Exception $e) {
            throw new RdvServiceException($e);
        }
    }

    // Cycle de vie du RDV

    public function marquerRdvHonore(string $rdv_id): RdvDTO {
        try {
            $rdv = $this->rdvRepository->getRdvById($rdv_id);
            if ($rdv->status !== 'prevu') {
                throw new RdvServiceException('Rdv ne peux pas être marqué comme honoré');
            }
            $rdv->setStatus("honore");
            $this->rdvRepository->update($rdv);
            $this->logger->log(Level::Info, "Modification RDV : " . $rdv_id. " - Status : Honoré");
            return new RdvDTO($rdv);
        } catch (\Exception $e) {
            throw new RdvServiceException($e);
        }
    }

    public function marquerRdvNonHonore(string $rdv_id): RdvDTO {
        try {
            $rdv = $this->rdvRepository->getRdvById($rdv_id);
            if ($rdv->status !== 'prevu') {
                throw new RdvServiceException('Rdv ne peux pas être marqué comme non honoré');
            }
            $rdv->setStatus("non_honore");
            $this->rdvRepository->update($rdv);
            $this->logger->log(Level::Info, "Modification RDV : " . $rdv_id. " - Status : Non Honoré");
            return new RdvDTO($rdv);
        } catch (\Exception $e) {
            throw new RdvServiceException($e);
        }
    }

    public function payerRdv(string $rdv_id): RdvDTO {
        try {
            $rdv = $this->rdvRepository->getRdvById($rdv_id);
            if ($rdv->status !== 'honore') {
                throw new RdvServiceException('Rdv ne peux pas être payé car il n\'est pas honoré');
            }
            $rdv->setStatus("paye");
            $this->rdvRepository->update($rdv);
            $this->logger->log(Level::Info, "Modification RDV : " . $rdv_id. " - Status : Payé");
            return new RdvDTO($rdv);
        } catch (\Exception $e) {
            throw new RdvServiceException($e);
        }
    }

    public function annulerRdv(string $rdv_id):RdvDTO{
        try {
            $rdv = $this->rdvRepository->getRdvById($rdv_id);
            if ($rdv->status !== "prevu") {
                throw new RdvServiceException('Pas possible d\'annuler le RDV');
            }
            $rdv->setStatus("annule");
            $this->rdvRepository->update($rdv);
            $this->logger->log(Level::Info, "Modification RDV : " . $rdv_id. " - Status : Annulé");
            return new RdvDTO($rdv);
        } catch (\Exception $e){
            throw new RdvServiceException($e);
        }
    }


    // Création de message pour le broker. Récupération des informations du RDV, du patient et du praticien
    public function getCreateRDVMessage(string $praticienId, string $patientId, string $rdv) : array {
        try {
            $patient =$this->patientService->getPatientById($id);
            return $patient->toDTO();
        }catch (\Exception $e){
            throw new RdvServiceException($e);
        }

        try {
            $praticien = $this->praticienService->getPraticienById($id);
            return $praticien->toDTO();
        } catch (\Exception $e) {
            throw new RdvServiceException($e);
        }

        $message = [
            "rdv" => $rdv,
            "patient" => $patient,
            "praticien" => $praticien
        ];

        return $message;
    }
}