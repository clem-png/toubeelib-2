<?php

namespace toubeelib_rdv\infrastructure\repositories;

use toubeelib_rdv\core\domain\entities\patient\Patient;
use toubeelib_rdv\core\domain\entities\praticien\Specialite;
use toubeelib_rdv\core\domain\entities\praticien\Praticien;
use toubeelib_rdv\core\services\praticien\ServicePraticienInterface;
use toubeelib_rdv\core\dto\PraticienDTO;
use toubeelib_rdv\core\dto\SpecialiteDTO;

use GuzzleHttp\Client;
use Monolog\Logger;
use Monolog\Level;
use Psr\Log\LoggerInterface;

class PraticienServiceAdapter implements ServicePraticienInterface
{
    private $client;
    private LoggerInterface $logger;

    public function __construct(Client $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
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

    public function getPatientById(string $id): Patient
    {
        $response = $this->client->get("/praticiens/{$id}");
        $data = json_decode($response->getBody()->getContents(), true);
        $data = $data['praticiens'];
        return new Patient($data['nom'], $data['prenom'], $data['adresse'], $data['mail'], $data['dateNaissance'], $data['numSecu'], $data['numeroTel']);
    }
}