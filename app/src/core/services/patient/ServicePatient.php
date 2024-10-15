<?php

namespace toubeelib\core\services\patient;

use Exception;
use Monolog\Level;
use Psr\Log\LoggerInterface;
use Respect\Validation\Rules\Date;
use toubeelib\core\domain\entities\patient\Patient;
use toubeelib\core\dto\InputPatientDTO;
use toubeelib\core\dto\PatientDTO;
use toubeelib\core\repositoryInterfaces\PatientRepositoryInterface;
use toubeelib\core\services\patient\ServicePatientInterface;

class ServicePatient implements ServicePatientInterface
{

    private PatientRepositoryInterface $patientRepository;

    private LoggerInterface $logger;

    public function __construct(PatientRepositoryInterface $patientRepository, LoggerInterface $logger)
    {
        $this->patientRepository = $patientRepository;
        $this->logger = $logger;
    }

    /**
     * @throws ServicePatientException
     */
    public function creerPatient(InputPatientDTO $DTO): PatientDTO
    {
        try{
            $nom = $DTO->nom;
            $prenom = $DTO->prenom;
            $dateNaissance = $DTO->dateNaissance;
            $adresse = $DTO->adresse;
            $mail = $DTO->mail;
            $numSecu = $DTO->numSecu;
            $tel = $DTO->numeroTel;
            $patient = new Patient($nom, $prenom, $adresse, $mail, $dateNaissance, $numSecu, $tel);
            $id = $this->patientRepository->save($patient);
            $patient->setID($id);
            $this->logger->log(Level::Info, "Creation patient : " . $id);
        }catch (Exception $e){
            $this->logger->log(Level::Error, "Erreur lors de la creation du patient : " . $e->getMessage());
            throw new ServicePatientException($e);
        }
        return new PatientDTO($patient);

    }

}