<?php

use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\TestCase;
use toubeelib\core\services\rdv\ServiceRdv;
use toubeelib\infrastructure\repositories\ArrayRdvRepository;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\RdvDTO;
use toubeelib\core\dto\SpecialiteDTO;
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
        $logger = new \Monolog\Logger('test.log');
        $logger->pushHandler(new StreamHandler(__DIR__.'/test.log',\Monolog\Level::Info));
        $this->praticienRepository = new ArrayPraticienRepository();
        $this->praticienService = new ServicePraticien($this->praticienRepository, $logger);

        $this->rdvRepository = new ArrayRdvRepository();
        $this->serviceRdv = new ServiceRdv($this->rdvRepository, $this->praticienService, $logger);

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
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 12:00",), "A");
        $result = $this->serviceRdv->creerRdv($DTO);

        $this->assertInstanceOf(RdvDTO::class, $result);

        $savedRdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame($DTO->idPatient, $savedRdv->idPatient);
        $this->assertSame($DTO->idPraticien, $savedRdv->idPraticien);
        $this->assertSame($DTO->dateDebut, $savedRdv->dateDebut);
        $this->assertSame($DTO->status, $savedRdv->status);

        //test création d'un RDV avec une spécialité
        $specialite = new SpecialiteDTO('A', 'Dentiste', 'Spécialiste des dents');
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",), "A", $specialite);
        $result = $this->serviceRdv->creerRdv($DTO);

        $this->assertInstanceOf(RdvDTO::class, $result);

        $savedRdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame($DTO->idPatient, $savedRdv->idPatient);
        $this->assertSame($DTO->idPraticien, $savedRdv->idPraticien);
        $this->assertSame($DTO->dateDebut, $savedRdv->dateDebut);
        $this->assertSame($DTO->status, $savedRdv->status);
        $this->assertSame($DTO->specialite->label, $savedRdv->specialite_label);

        // Test exception si le praticien n'existe pas
        $DTO = new InputRdvDTO("testid", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 12:00",), "A");
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->creerRdv($DTO);

        // Test exception si le créneau n'est pas disponible
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 09:00",), "A");
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->creerRdv($DTO);

        // Test exception si la spécialité n'est pas valide
        $specialite = new SpecialiteDTO('B', 'Ophtalmologue', 'Spécialiste des yeux');
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",), "A", $specialite);
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->creerRdv($DTO);
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

    public function testModifierPatientRdv()
    {
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",), "A");
        $result = $this->serviceRdv->creerRdv($DTO);

        // Test existence du RDV
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('A', $rdv->status);
        $this->assertSame('pa1', $rdv->idPatient);

        // Test modification du patient
        $this->serviceRdv->modifierPatientOuSpecialiteRdv($rdv->id, 'pa2');
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('pa2', $rdv->idPatient);

        // Test exception si le RDV n'existe pas
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->modifierPatientOuSpecialiteRdv('testid', 'pa2');
    }

    public function testModifierSpecialiteRdv()
    {
        $specialite = new SpecialiteDTO('A', 'Dentiste', 'Spécialiste des dents');
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",), "A", $specialite);
        $result = $this->serviceRdv->creerRdv($DTO);

        // Test existence du RDV
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('Dentiste', $rdv->specialite_label);

        // Test modification de la spécialité
        $specialite = new SpecialiteDTO('B', 'Ophtalmologue', 'Spécialiste des yeux');
        $this->serviceRdv->modifierPatientOuSpecialiteRdv($rdv->id, null, $specialite);

        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('Ophtalmologue', $rdv->specialite_label);

        // Test exception si le RDV n'existe pas
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->modifierPatientOuSpecialiteRdv('testid', null, $specialite);
    }

    public function testModifierSpecialiteEtPatientRdv(){
        $specialite = new SpecialiteDTO('A', 'Dentiste', 'Spécialiste des dents');
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",), "A", $specialite);
        $result = $this->serviceRdv->creerRdv($DTO);

        // Test existence du RDV
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('Dentiste', $rdv->specialite_label);
        $this->assertSame('pa1', $rdv->idPatient);

        // Test modification de la spécialité et du patient
        $specialite = new SpecialiteDTO('B', 'Ophtalmologue', 'Spécialiste des yeux');
        $this->serviceRdv->modifierPatientOuSpecialiteRdv($rdv->id, 'pa2', $specialite);

        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('Ophtalmologue', $rdv->specialite_label);
        $this->assertSame('pa2', $rdv->idPatient);

        // Test exception si le RDV n'existe pas
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->modifierPatientOuSpecialiteRdv('testid', 'pa2', $specialite);
    }
}