<?php

use PHPUnit\Framework\TestCase;
use toubeelib\core\services\rdv\ServiceRdv;
use toubeelib\infrastructure\repositories\ArrayRdvRepository;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\RdvDTO;
use toubeelib\core\services\rdv\RdvServiceException;
use toubeelib\infrastructure\repositories\ArrayPraticienRepository;
use toubeelib\core\services\praticien\ServicePraticien;

class ServiceRdvTest extends TestCase
{
    private $rdvRepository;
    private $praticienRepository;
    private $praticienService;
    private $serviceRdv;

    protected function setUp(): void
    {
        $this->praticienRepository = new ArrayPraticienRepository();
        $this->praticienService = new ServicePraticien($this->praticienRepository);

        $this->rdvRepository = new ArrayRdvRepository();
        $this->serviceRdv = new ServiceRdv($this->rdvRepository, $this->praticienService);

    }

    public function testConsulterRdv()
    {
        $rdv_id = 'r1';
        $result = $this->serviceRdv->consulterRdv($rdv_id);

        $this->assertSame('pa1',$result->idPatient);
        $this->assertSame('p1', $result->idPraticien);

        $rdv_id = 'testid';
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->consulterRdv($rdv_id);
    }

    public function testCreerRdv()
    {
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 09:00",), "A");

        $result = $this->serviceRdv->creerRdv($DTO);

        $this->assertInstanceOf(RdvDTO::class, $result);

        $savedRdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame($DTO->idPatient, $savedRdv->idPatient);
        $this->assertSame($DTO->idPraticien, $savedRdv->idPraticien);
        $this->assertSame($DTO->dateDebut, $savedRdv->dateDebut);
        $this->assertSame($DTO->status, $savedRdv->status);
    }

    public function testListerDisponibilitePraticien()
    {
        $dateDebut = new DateTime('2024-09-02');
        $dateFin = new DateTime('2024-09-02');
        $id = 'p1';

        $result = $this->serviceRdv->listerDisponibilitePraticien($dateDebut, $dateFin, $id);

        /*
        9h - 17h avec des RDV de 30min = 16 créneaux
        Le praticien p1 a un RDV à 9h et un RDV à 10h
        Il reste donc 14 créneaux disponibles 
        */

        $this->assertCount(14, $result);

        // Test exception si le praticien n'existe pas
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->listerDisponibilitePraticien($dateDebut, $dateFin, 'testid');

        // Test exception si la date de fin est inférieure à la date de début
        $dateDebut = new DateTime('2024-09-02');
        $dateFin = new DateTime('2024-09-01');
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->listerDisponibilitePraticien($dateDebut, $dateFin, $id);
    }

    public function testAnnulerRdv()
    {
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",), "A");
        $result = $this->serviceRdv->creerRdv($DTO);

        // Test existence du RDV
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('A', $rdv->status);

        // Test annulation du RDV
        $this->serviceRdv->annulerRdv($result->id);
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('Cancelled', $rdv->status);

        // Test exception si le RDV est déjà annulé
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->annulerRdv($result->id);

        // Test exception si le RDV n'existe pas
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->annulerRdv('testid');
    }
}