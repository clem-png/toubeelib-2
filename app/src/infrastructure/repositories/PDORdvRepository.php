<?php

namespace toubeelib\infrastructure\repositories;

use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\rdv\Rdv;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class PDORdvRepository implements RdvRepositoryInterface
{

    private \PDO $pdo;


    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getRdvById(string $id): Rdv
    {
        $stmt = $this->pdo->prepare('SELECT * FROM rdv WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $rdv = $stmt->fetch();
        if (!$rdv) {
            throw new RepositoryEntityNotFoundException('Rdv not found');
        }
        $rdvReturn = new Rdv($rdv['idPraticien'], $rdv['IdPatient'], $rdv['status'], \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $rdv['dateDebut']), $rdv['type']);
        $rdvReturn->setID($rdv['id']);
        return $rdvReturn;
    }

    public function save(Rdv $rdv): string
    {
        $ID = Uuid::uuid4()->toString();
        $idPraticien = $rdv->idPraticien;
        $idPatient = $rdv->idPatient;
        $status = $rdv->status;
        $dateDebut = $rdv->dateDebut->format('Y-m-d H:i:s');
        $idSpe = $rdv->specialite->ID;
        $type = $rdv->type;
        $stmt = $this->pdo->prepare('INSERT INTO rdv (id, "idPraticien", "IdPatient", status,"idSpe", "dateDebut",type ) VALUES (?, ?, ?,?, ?, ?, ?)');
        $stmt->bindParam(1, $ID);
        $stmt->bindParam(2, $idPraticien);
        $stmt->bindParam(3, $idPatient);
        $stmt->bindParam(4, $status);
        $stmt->bindParam(5, $idSpe);
        $stmt->bindParam(6, $dateDebut);
        $stmt->bindParam(7, $type);
        $stmt->execute();
        return $ID;
    }

    public function getRdvByPatientId(string $id): array
    {
        $rdvs = [];
        $stmt = $this->pdo->prepare('SELECT * FROM rdv where "IdPatient" = ?');
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $rdvsRes = $stmt->fetchAll();
        foreach ($rdvsRes as $rdv) {
            $rdvObj = new Rdv($rdv['idPraticien'], $rdv['IdPatient'], $rdv['status'], \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $rdv['dateDebut']), $rdv['type']);
            $rdvObj->setID($rdv['id']);
            //rajouter l'element dans le tableau
            $rdvs[] = $rdvObj;
        }
        return $rdvs;
    }

    public function update(Rdv $rdv): void
    {
        $ID = $rdv->getID();
        $stmt = $this->pdo->prepare('Select * from rdv where id = ?');
        $stmt->bindParam(1, $ID);
        $stmt->execute();
        $rdvExist = $stmt->fetch();

        if (!$rdvExist) {
            throw new RepositoryEntityNotFoundException('Rdv not found');
        }
        $idPraticien = $rdv->idPraticien;
        $idPatient = $rdv->idPatient;
        $status = $rdv->status;
        $dateDebut = $rdv->dateDebut->format('Y-m-d H:i:s');


        $stmt = $this->pdo->prepare('UPDATE rdv SET "idPraticien" = ?, "IdPatient" = ?, status = ?, "dateDebut" = ? WHERE id = ?');
        $stmt->bindParam(1, $idPraticien);
        $stmt->bindParam(2, $idPatient);
        $stmt->bindParam(3, $status);
        $stmt->bindParam(4, $dateDebut);
        $stmt->bindParam(5, $ID);
        $stmt->execute();
    }

    public function getRdvByPraticienId(string $id): array
    {
        $rdvs = [];
        $stmt = $this->pdo->prepare('SELECT * FROM rdv WHERE "idPraticien" = ?');
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $rdvsRes = $stmt->fetchAll();
        foreach ($rdvsRes as $rdv) {
            $rdvObj = new Rdv($rdv['idPraticien'], $rdv['IdPatient'], $rdv['status'], \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $rdv['dateDebut']), $rdv['type']);
            $rdvObj->setID($rdv['id']);
            $rdvs[] = $rdvObj;
        }
        return $rdvs;
    }

    public function getRdvPraticien(string $id, \DateTime $dateDebut, \DateTime $dateFin, string $idSpe, string $type): array
    {
        $rdvs = [];
        $stmt = $this->pdo->prepare('SELECT * FROM rdv WHERE "idPraticien" = ? AND "dateDebut" >= ? AND "dateDebut" <= ? AND "idSpe" = ? AND "type" = ?');
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $dateDebut->format('Y-m-d H:i:s'));
        $stmt->bindParam(3, $dateFin->format('Y-m-d H:i:s'));
        $stmt->bindParam(4, $idSpe);
        $stmt->bindParam(5, $type);
        $stmt->execute();
        $rdvsRes = $stmt->fetchAll();
        foreach ($rdvsRes as $rdv) {
            $rdvObj = new Rdv($rdv['idPraticien'], $rdv['IdPatient'], $rdv['status'], \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $rdv['dateDebut']), $rdv['type']);
            $rdvObj->setID($rdv['id']);
            $rdvs[] = $rdvObj;
        }
        return $rdvs;
    }
}