<?php

use PHPUnit\Framework\TestCase;
use toubeelib\core\services\rdv\ServiceRdv;
use toubeelib\infrastructure\repositories\ArrayRdvRepository;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\RdvDTO;
use toubeelib\core\services\rdv\RdvServiceException;

class ServiceRdvTest extends TestCase
{
    private $rdvRepository;
    private $serviceRdv;

    protected function setUp(): void
    {
        $this->rdvRepository = new ArrayRdvRepository();
        $this->serviceRdv = new ServiceRdv($this->rdvRepository);
    }

    public function testConsulterRdv()
    {
        $rdv_id = 'r1';
        $result = $this->serviceRdv->consulterRdv($rdv_id);

        $this->assertSame($result->idPraticien, 'pa1');
        $this->assertSame($result->idPatient, 'p1');

        $rdv_id = 'testid';
        $this->expectException(RdvServiceException::class);
        $this->serviceRdv->consulterRdv($rdv_id);
    }

    public function testCreerRdv()
    {
        $DTO = new InputRdvDTO("pTest", "paTest", \DateTimeImmutable::createFromFormat('Y-m-d H:i',  "2024-09-02 09:00",), "A");

        $result = $this->serviceRdv->creerRdv($DTO);

        $this->assertInstanceOf(RdvDTO::class, $result);

        $savedRdv = $this->serviceRdv->consulterRdv($result->id);
        $this->assertSame($DTO->idPatient, $savedRdv->idPatient);
        $this->assertSame($DTO->idPraticien, $savedRdv->idPraticien);
        $this->assertSame($DTO->dateDebut, $savedRdv->dateDebut);
        $this->assertSame($DTO->status, $savedRdv->status);
    }
}