<?php

namespace toubeelib_rdv\infrastructure\repositories;

use toubeelib_rdv\core\services\praticien\ServicePraticienInterface;
use toubeelib_rdv\core\dto\PraticienDTO;
use toubeelib_rdv\core\dto\SpecialiteDTO;

use guzzlehttp\client;

class PraticienServiceAdapter implements ServicePraticienInterface
{
    private $client;

    public function __construct(client $client)
    {
        $this->client = $client;
    }

    public function getPraticienById(string $id): PraticienDTO
    {
        $response = $this->client->get("/praticiens/{$id}");
        $data = json_decode($response->getBody()->getContents(), true);
        return new PraticienDTO($data['praticien']);
    }

    public function getSpecialiteById(string $id): SpecialiteDTO
    {
        $response = $this->client->get("/specialites/{$id}");
        $data = json_decode($response->getBody()->getContents(), true);
        return new SpecialiteDTO($data['specialite']);
    }
}