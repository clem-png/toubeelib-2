<?php

namespace toubeelib_patient\infrastructure\repositories;

use Exception;
use PDO;
use Ramsey\Uuid\Uuid;
use toubeelib_patient\core\domain\entities\patient\Patient;
use toubeelib_patient\core\repositoryInterfaces\PatientRepositoryInterface;
use toubeelib_patient\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class PDOPatientRepository implements PatientRepositoryInterface
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function save(Patient $patient): string
    {
        try {
            $ID = Uuid::uuid4()->toString();
            $nom = $patient->nom;
            $prenom = $patient->prenom;
            $adresse = $patient->adresse;
            $dateNaissance = $patient->dateNaissance;
            $mail = $patient->mail;
            $numSecu = $patient->numSecu;
            $stmt = $this->pdo->prepare('INSERT INTO patient (id, "num_secu", "date_naissance", nom, prenom, adresse, mail) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->bindParam(1, $ID);
            $stmt->bindParam(2, $numSecu);
            $stmt->bindParam(3, $dateNaissance);
            $stmt->bindParam(4, $nom);
            $stmt->bindParam(5, $prenom);
            $stmt->bindParam(6, $adresse);
            $stmt->bindParam(7, $mail);
            $stmt->execute();
        } catch (Exception $e) {
            throw new RepositoryEntityNotFoundException($e->getMessage());
        }

        $num = $patient->numeroTel;
        try {
            // vérifier si le numéro est déjà dans la base
            $stmt = $this->pdo->prepare('SELECT * FROM "num_patient" WHERE numero = ?');
            $stmt->bindParam(1, $num);
            $stmt->execute();
            $existingNum = $stmt->fetch();
            if ($existingNum != null) {
                throw new RepositoryEntityNotFoundException("Le numéro de téléphone {$num} est déjà utilisé");
            }
            $stmt = $this->pdo->prepare('INSERT INTO "num_patient" ("idPatient", numero) VALUES (?, ?)');
            $stmt->bindParam(1, $ID);
            $stmt->bindParam(2, $num);
            $stmt->execute();
        } catch (Exception $e) {
            throw new RepositoryEntityNotFoundException($e->getMessage());
        }
        return $ID;

    }

  /**
   * @throws RepositoryEntityNotFoundException
   */
  public function getPatient(string $id) : Patient
  {
      $stmt = $this->pdo->prepare('SELECT * FROM patient WHERE ID = ?'); // Utiliser = au lieu de LIKE
      $stmt->bindValue(1, $id, PDO::PARAM_STR); // Utiliser bindValue
      $stmt->execute();

      $patient = $stmt->fetch();
      if (!$patient) {
          throw new RepositoryEntityNotFoundException("Patient not found");
      }
      $stmtNum = $this->pdo->prepare('SELECT * FROM num_patient WHERE "idPatient" = ?'); // Utiliser = ici aussi
      $stmtNum->bindValue(1, $patient['id'], PDO::PARAM_STR); // Utiliser bindValue
      $stmtNum->execute();

      $numPatient = $stmtNum->fetch(PDO::FETCH_ASSOC);
      if (!$numPatient) {
          throw new RepositoryEntityNotFoundException("Numero not found");
      }

      $p = new Patient(
          $patient['nom'],
          $patient['prenom'],
          $patient['adresse'],
          $patient['mail'],
          $patient['date_naissance'],
          $patient['num_secu'],
          $numPatient['numero']
      );

      $p->setID($patient['id']);
      return $p;
  }

}