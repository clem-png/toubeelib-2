<?php

use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\TestCase;
use toubeelib_praticiens\core\services\rdv\ServiceRdv;
use toubeelib_praticiens\core\dto\InputRdvDTO;
use toubeelib_praticiens\core\dto\RdvDTO;
use toubeelib_praticiens\core\dto\InputSpecialiteDTO;
use toubeelib_praticiens\core\services\rdv\RdvServiceException;
use toubeelib_praticiens\core\services\praticien\ServicePraticien;

use toubeelib_praticiens\infrastructure\repositories\PDOPraticienRepository;
use toubeelib_praticiens\infrastructure\repositories\PDORdvRepository;


class ServiceRdvPDOTest extends TestCase
{
    private $rdvRepository;
    private $praticienRepository;
    private $praticienService;
    private $serviceRdv;
    private $pdoPraticien;
    private $pdoRdv;

    protected function setUp(): void
    {
        $logger = new \Monolog\Logger('test.log');
        $logger->pushHandler(new StreamHandler(__DIR__.'/test.log',\Monolog\Level::Info));

        $config = parse_ini_file(__DIR__.'/../config/iniconf/praticien.db.ini');
        $dsn = "{$config['driver']}:host=localhost;port={$config['port']};dbname={$config['database']}";
        $user = $config['username'];
        $password = $config['password'];
        $this->pdoPraticien = new PDO($dsn, $user, $password);
        $this->praticienRepository = new PDOPraticienRepository($this->pdoPraticien);
        $this->praticienService = new ServicePraticien($this->praticienRepository, $logger);

        $config = parse_ini_file(__DIR__.'/../config/iniconf/rdv.db.ini');
        $dsn = "{$config['driver']}:host=localhost;port={$config['port']};dbname={$config['database']}";
        $user = $config['username'];
        $password = $config['password'];
        $this->pdoRdv = new \PDO($dsn, $user, $password);
        $this->rdvRepository = new PDORdvRepository($this->pdoRdv);
        $this->serviceRdv = new ServiceRdv($this->rdvRepository, $this->praticienService, $logger);

    }

    public function testConsulterRdv()
    {
        $rdv_id = '89695d04-74eb-4b82-a0bd-b8b802803c57';
        $result = $this->serviceRdv->consulterRdv($rdv_id);

        $this->assertSame('d4ac898d-0d47-48d6-b354-8282bec927ba',$result->idPatient);
        $this->assertSame('cb771755-26f4-4e6c-b327-a1217f5b09cd', $result->idPraticien);

        $rdv_id = 'testid';
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->consulterRdv($rdv_id);
    }

    public function testCreerRdv()
    {
        $DTO = new InputRdvDTO("8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c", "99999999-9999-9999-9999-999999999999", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 12:00",), 'presentiel');
        $result = $this->serviceRdv->creerRdv($DTO);

        $this->assertInstanceOf(RdvDTO::class, $result);

        $savedRdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame($DTO->idPatient, $savedRdv->idPatient);
        $this->assertSame($DTO->idPraticien, $savedRdv->idPraticien);


        //test création d'un RDV avec une spécialité
        $specialite = new InputSpecialiteDTO('6542592f-8aaf-4ba5-9859-2169a686e9ae');
        $DTO = new InputRdvDTO("8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c", "99999999-9999-9999-9999-999999999999", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",), 'presentiel', $specialite);
        $result = $this->serviceRdv->creerRdv($DTO);

        $this->assertInstanceOf(RdvDTO::class, $result);

        $savedRdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame($DTO->idPatient, $savedRdv->idPatient);
        $this->assertSame($DTO->idPraticien, $savedRdv->idPraticien);

        // Test exception si le praticien n'existe pas
        $DTO = new InputRdvDTO("testid", "99999999-9999-9999-9999-999999999999", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 12:00",), 'presentiel');
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->creerRdv($DTO);

        // Test exception si le créneau n'est pas disponible
        $DTO = new InputRdvDTO("8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c", "99999999-9999-9999-9999-999999999999", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 09:00",), 'presentiel');
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->creerRdv($DTO);

        // Test exception si la spécialité n'est pas valide
        $specialite = new InputSpecialiteDTO('B');
        $DTO = new InputRdvDTO("8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c", "99999999-9999-9999-9999-999999999999", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",), 'presentiel', $specialite);
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->creerRdv($DTO);
    }

    public function testListerDisponibilitePraticien()
    {
        $dateDebut = new DateTime('2021-01-01');
        $dateFin = new DateTime('2021-01-01');
        $id = 'cb771755-26f4-4e6c-b327-a1217f5b09cd';

        $result = $this->serviceRdv->listerDisponibilitePraticien($dateDebut, $dateFin, $id);

        /*
        7h - 17h avec des RDV de 30min = 20 créneaux
        Il reste donc 19 créneaux disponibles 
        */

        $this->assertCount(19, $result);

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
        $DTO = new InputRdvDTO("8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c", "99999999-9999-9999-9999-999999999999", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 15:00",), 'presentiel');
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
        $DTO = new InputRdvDTO("8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c", "99999999-9999-9999-9999-999999999999", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 16:30",), 'presentiel');
        $result = $this->serviceRdv->creerRdv($DTO);

        // Test existence du RDV
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('prevu', $rdv->status);
        $this->assertSame('99999999-9999-9999-9999-999999999999', $rdv->idPatient);

        // Test modification du patient
        $this->serviceRdv->modifierPatientOuSpecialiteRdv($rdv->id, '99999999-9999-9999-9999-999999999998');
        $rdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame('99999999-9999-9999-9999-999999999998', $rdv->idPatient);

        // Test exception si le RDV n'existe pas
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->modifierPatientOuSpecialiteRdv('testid', '99999999-9999-9999-9999-999999999998');
    }

    public function testMarquerRdvHonore()
    {
        $DTO = new InputRdvDTO("8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c", "99999999-9999-9999-9999-999999999999", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-01-01 15:30",), 'presentiel');
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
        $DTO = new InputRdvDTO("8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c", "99999999-9999-9999-9999-999999999999", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-06 13:30",), 'presentiel');
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
        $DTO = new InputRdvDTO("8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c", "99999999-9999-9999-9999-999999999999", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-03 13:30",), 'presentiel');
        $result = $this->serviceRdv->creerRdv($DTO);

        // Marqué le RDV comme honoré
        $this->serviceRdv->marquerRdvHonore($result->id);

        // Test exception si éssayer d'annuler un RDV déjà honoré
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->annulerRdv($result->id);

        // marqué le RDV comme non honoré
        $DTO = new InputRdvDTO("8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c", "99999999-9999-9999-9999-999999999999", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-03 14:30",));
        $result = $this->serviceRdv->creerRdv($DTO);
        $this->serviceRdv->marquerRdvNonHonore($result->id);

        // Test exception si éssayer d'annuler un RDV déjà non honoré
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->annulerRdv($result->id);
    }

    public function testMarquerHonoreOuNonHonoreRdvDejaAnnule()
    {
        $DTO = new InputRdvDTO("8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c", "99999999-9999-9999-9999-999999999999", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-04 13:30",), 'presentiel');
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
        $result = $this->serviceRdv->listerRdvPatient('d4ac898d-0d47-48d6-b354-8282bec927ba');
        $this->assertCount(1, $result);

        // Test lister les RDVs pour un autre patient
        $result = $this->serviceRdv->listerRdvPatient('d8bfdec3-ca7c-4e72-98f2-749e5f775c86');
        $this->assertCount(1, $result);

        // Test lister les RDVs pour un patient qui n'a pas de RDV
        $result = $this->serviceRdv->listerRdvPatient('99999999-9999-9999-9999-999999999999');
        $this->assertCount(0, $result);
    }

    public function testPayerRdv()
    {
        // Test payer un RDV honoré
        $DTO = new InputRdvDTO("8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c", "99999999-9999-9999-9999-999999999999", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 13:30",), 'presentiel');
        $result = $this->serviceRdv->creerRdv($DTO);
        $this->serviceRdv->marquerRdvHonore($result->id);
        $rdv = $this->serviceRdv->payerRdv($result->id);

        $this->assertSame('paye', $rdv->status);

        // Test exception payer un RDV déjà payé
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->payerRdv($result->id);

        // Test exception payer un RDV qui n'éxiste pas
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->payerRdv('testid');
    }

    public function testAfficherPlanningPraticien()
    {
        $dateDebut = new DateTime('2021-01-01 08:00:00');
        $dateFin = new DateTime('2021-01-02 23:59:59');
        $id = 'cb771755-26f4-4e6c-b327-a1217f5b09cd';
        $idSpe = 'dd3cac4c-c175-427c-b2aa-8fcc54f250b5';
        $type = 'presentiel';
    
        $result = $this->serviceRdv->afficherPlanningPraticien($dateDebut, $dateFin, $id, $idSpe, $type);
    
        // Test if the planning is correctly retrieved
        $this->assertCount(1, $result);
    }

    protected function tearDown(): void
    {
        $this->pdoRdv->exec('DELETE FROM rdv WHERE "IdPatient" = \'99999999-9999-9999-9999-999999999999\'');
        $this->pdoRdv->exec('DELETE FROM rdv WHERE "IdPatient" = \'99999999-9999-9999-9999-999999999998\'');
    }
}