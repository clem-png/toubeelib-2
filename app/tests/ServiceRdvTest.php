<?php

use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\TestCase;
use toubeelib\core\services\rdv\ServiceRdv;
use toubeelib\infrastructure\repositories\ArrayRdvRepository;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\RdvDTO;
use toubeelib\core\dto\InputSpecialiteDTO;
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
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 12:00",));
        $result = $this->serviceRdv->creerRdv($DTO);

        $this->assertInstanceOf(RdvDTO::class, $result);

        $savedRdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame($DTO->idPatient, $savedRdv->idPatient);
        $this->assertSame($DTO->idPraticien, $savedRdv->idPraticien);
        $this->assertSame($DTO->dateDebut, $savedRdv->dateDebut);

        //test création d'un RDV avec une spécialité
        $specialite = new InputSpecialiteDTO('A');
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",), $specialite);
        $result = $this->serviceRdv->creerRdv($DTO);

        $this->assertInstanceOf(RdvDTO::class, $result);

        $savedRdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame($DTO->idPatient, $savedRdv->idPatient);
        $this->assertSame($DTO->idPraticien, $savedRdv->idPraticien);
        $this->assertSame($DTO->dateDebut, $savedRdv->dateDebut);

        // Test exception si le praticien n'existe pas
        $DTO = new InputRdvDTO("testid", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 12:00",));
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->creerRdv($DTO);

        // Test exception si le créneau n'est pas disponible
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 09:00",));
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->creerRdv($DTO);

        // Test exception si la spécialité n'est pas valide
        $specialite = new InputSpecialiteDTO('B');
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",), $specialite);
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
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",));
        $result = $this->serviceRdv->creerRdv($DTO);

        // Test existence du RDV
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('prevu', $rdv->status);

        // Test annulation du RDV
        $this->serviceRdv->annulerRdv($result->id);
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('annule', $rdv->status);

        // Test exception si le RDV est déjà annulé
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->annulerRdv($result->id);

        // Test exception si le RDV n'existe pas
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->annulerRdv('testid');
    }

    public function testModifierPatientRdv()
    {
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",));
        $result = $this->serviceRdv->creerRdv($DTO);

        // Test existence du RDV
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('prevu', $rdv->status);
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
        $specialite = new InputSpecialiteDTO('A');
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",), $specialite);
        $result = $this->serviceRdv->creerRdv($DTO);

        // Test existence du RDV
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('Dentiste', $rdv->specialite_label);

        // Test modification de la spécialité
        $specialite = new InputSpecialiteDTO('B');
        $this->serviceRdv->modifierPatientOuSpecialiteRdv($rdv->id, null, $specialite);

        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('Ophtalmologue', $rdv->specialite_label);

        // Test exception si le RDV n'existe pas
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->modifierPatientOuSpecialiteRdv('testid', null, $specialite);
    }

    public function testModifierSpecialiteEtPatientRdv(){
        $specialite = new InputSpecialiteDTO('A');
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",), $specialite);
        $result = $this->serviceRdv->creerRdv($DTO);

        // Test existence du RDV
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('Dentiste', $rdv->specialite_label);
        $this->assertSame('pa1', $rdv->idPatient);

        // Test modification de la spécialité et du patient
        $specialite = new InputSpecialiteDTO('B');
        $this->serviceRdv->modifierPatientOuSpecialiteRdv($rdv->id, 'pa2', $specialite);

        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('Ophtalmologue', $rdv->specialite_label);
        $this->assertSame('pa2', $rdv->idPatient);

        // Test exception si le RDV n'existe pas
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->modifierPatientOuSpecialiteRdv('testid', 'pa2', $specialite);
    }

    public function testMarquerRdvHonore()
    {
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",));
        $result = $this->serviceRdv->creerRdv($DTO);

        // Test marqué le RDV comme honoré
        $rdv = $this->serviceRdv->marquerRdvHonore($result->id);
        $this->assertSame('honore', $rdv->status);

        // Test exception si le RDV n'est pas en statut 'prevu'
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->marquerRdvHonore($result->id);

        // Test exception si le RDV n'existe pas
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->marquerRdvHonore('testid');
    }

    public function testMarquerRdvNonHonore()
    {
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",));
        $result = $this->serviceRdv->creerRdv($DTO);

        // Test marquer le RDV comme non honoré
        $rdv = $this->serviceRdv->marquerRdvNonHonore($result->id);
        $this->assertSame('non_honore', $rdv->status);

        // Test exception si le RDV n'est pas en statut 'prevu'
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->marquerRdvNonHonore($result->id);

        // Test exception si le RDV n'existe pas
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->marquerRdvNonHonore('testid');
    }

    public function testAnnulerRdvDejaHonoreOuNonHonore()
    {
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",));
        $result = $this->serviceRdv->creerRdv($DTO);

        // Marqué le RDV comme honoré
        $this->serviceRdv->marquerRdvHonore($result->id);

        // Test exception si éssayer d'annuler un RDV déjà honoré
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->annulerRdv($result->id);

        // marqué le RDV comme non honoré
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 14:30",));
        $result = $this->serviceRdv->creerRdv($DTO);
        $this->serviceRdv->marquerRdvNonHonore($result->id);

        // Test exception si éssayer d'annuler un RDV déjà non honoré
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->annulerRdv($result->id);
    }

    public function testMarquerHonoreOuNonHonoreRdvDejaAnnule()
    {
        $DTO = new InputRdvDTO("p1", "pa1", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",));
        $result = $this->serviceRdv->creerRdv($DTO);

        // Test annuler le RDV
        $this->serviceRdv->annulerRdv($result->id);
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('annule', $rdv->status);

        // Test exception si éssayer de marquer un RDV déjà annulé comme honoré
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->marquerRdvHonore($result->id);

        // Test exception si éssayer de marquer un RDV déjà annulé comme non honoré
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->marquerRdvNonHonore($result->id);
    }

    public function testListerRdvPatient()
    {
        // Test lister les RDVs pour un patient
        $result = $this->serviceRdv->listerRdvPatient('pa1');
        $this->assertCount(3, $result);

        // Test lister les RDVs pour un autre patient
        $result = $this->serviceRdv->listerRdvPatient('pa2');
        $this->assertCount(1, $result);

        // Test lister les RDVs pour un patient qui n'a pas de RDV
        $result = $this->serviceRdv->listerRdvPatient('pa3');
        $this->assertCount(0, $result);
    }
}