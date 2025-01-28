<?php

namespace toubeelib_rdv\infrastructure\repositories;

use toubeelib_rdv\core\domain\entities\praticien\Specialite;
use toubeelib_rdv\core\domain\entities\praticien\Praticien;
use toubeelib_rdv\core\services\praticien\ServicePraticienInterface;
use toubeelib_rdv\core\dto\PraticienDTO;
use toubeelib_rdv\core\dto\SpecialiteDTO;

use GuzzleHttp\Client;

class PraticienServiceAdapter implements ServicePraticienInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getPraticienById(string $id): Praticien
    {
        $response = $this->client->get("/praticiens/{$id}");
        $data = json_decode($response->getBody()->getContents(), true);
        $data = $data['praticien'];
        $praticien = new Praticien($data['nom'], $data['prenom'], $data['adresse'], $data['tel']);
        $praticien->setID($data['ID']);
        $praticien->setSpecialite($data['specialite_label']);

        return $praticien;
    }

    public function getSpecialiteById(string $id): Specialite
    {
        $response = $this->client->get("/specialites/{$id}");
        $data = json_decode($response->getBody()->getContents(), true);
        $data = $data['specialite'];
        return new Specialite($data['ID'], $data['label'], $data['description']);
    }
}