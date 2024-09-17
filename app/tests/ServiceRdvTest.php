<?php

use PHPUnit\Framework\TestCase;
use toubeelib\core\services\rdv\ServiceRdv;
use toubeelib\infrastructure\repositories\ArrayRdvRepository;
use toubeelib\core\dto\InputRdvDTO;

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
    }
}