<?php

namespace toubeelib\infrastructure\repositories;

use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\core\domain\entities\rdv\Rdv;
use toubeelib\core\domain\entities\rdv\RendezVous;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class ArrayRdvRepository implements RdvRepositoryInterface
{
    private array $rdvs = [];

    public function __construct() {
            $r1 = new Rdv('p1', 'pa1', 'prevu', \DateTimeImmutable::createFromFormat('Y-m-d H:i','2024-09-02 09:00') );
            $r1->setID('r1');
            $r2 = new Rdv('p1', 'pa1', 'prevu', \DateTimeImmutable::createFromFormat('Y-m-d H:i','2024-09-02 10:00'));
            $r2->setID('r2');
            $r3 = new Rdv('p2', 'pa1', 'prevu', \DateTimeImmutable::createFromFormat('Y-m-d H:i','2024-09-02 09:30'));
            $r3->setID('r3');
            $r4 = new Rdv('p2', 'pa2', 'prevu', \DateTimeImmutable::createFromFormat('Y-m-d H:i','2024-09-02 10:30'));
            $r4->setID('r4');

        $this->rdvs  = ['r1'=> $r1, 'r2'=>$r2, 'r3'=> $r3, 'r4'=>$r4];
    }


    public function getRdvById(string $id): Rdv
    {
        if (!array_key_exists($id, $this->rdvs)) {
            throw new RepositoryEntityNotFoundException('Rdv not found');
        }
        return $this->rdvs[$id];
    }

    public function getRdvByPraticienId(string $id): array
    {
        $rdvs = [];
        foreach ($this->rdvs as $rdv) {
            if ($rdv->idPraticien === $id) {
                $rdvs[] = $rdv;
            }
        }
        return $rdvs;
    }

    public function save(Rdv $rdv): string
    {
        $ID = Uuid::uuid4()->toString();
        $rdv->setID($ID);
        $this->rdvs[$ID] = $rdv;
        return $ID;
    }

    public function update(Rdv $rdv): void
    {
        if (!array_key_exists($rdv->getID(), $this->rdvs)) {
            throw new RepositoryEntityNotFoundException('Rdv not found');
        }
        $this->rdvs[$rdv->getID()] = $rdv;
    }

    public function getRdvByPatientId(string $id): array
    {
        $rdvs = [];
        foreach ($this->rdvs as $rdv) {
            if ($rdv->idPatient === $id) {
                $rdvs[] = $rdv;
            }
        }
        return $rdvs;
    }
}