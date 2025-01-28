<?php

namespace toubeelib_patient\core\services\patient;

use Exception;
use Monolog\Level;
use Psr\Log\LoggerInterface;
use Respect\Validation\Rules\Date;
use toubeelib\core\dto\PraticienDTO;
use toubeelib_patient\core\domain\entities\patient\Patient;
use toubeelib_patient\core\dto\InputPatientDTO;
use toubeelib_patient\core\dto\PatientDTO;
use toubeelib_patient\core\repositoryInterfaces\PatientRepositoryInterface;
use toubeelib_patient\core\services\patient\ServicePatientInterface;

class ServicePatient implements ServicePatientInterface
{

    private PatientRepositoryInterface $patientRepository;


    public function __construct(PatientRepositoryInterface $patientRepository)
    {
        $this->patientRepository = $patientRepository;
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
        }catch (Exception $e){
            throw new ServicePatientException($e);
        }
        return new PatientDTO($patient);

    }

  /**
   * @throws ServicePatientException
   */
  public function getPatientById(string $id): PatientDTO
  {
    try{
      $patient = $this->patientRepository->getPatient($id);
      return new PatientDTO($patient);
    }catch (Exception $e){
      throw new ServicePatientException($e);
    }
  }

}