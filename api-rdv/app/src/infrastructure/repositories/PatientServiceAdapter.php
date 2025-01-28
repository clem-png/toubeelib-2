<?php

namespace toubeelib_rdv\infrastructure\repositories;

use toubeelib_rdv\core\domain\entities\patient\Patient;
use toubeelib_rdv\core\services\patient\ServicePatientInterface;


use GuzzleHttp\Client;


class PatientServiceAdapter implements ServicePatientInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getPatientById(string $id): Patient
    {
        $response = $this->client->get("/patients/{$id}");
        $data = json_decode($response->getBody()->getContents(), true);
        $data = $data['praticiens'];
        return new Patient($data['nom'], $data['prenom'], $data['adresse'], $data['mail'], $data['dateNaissance'], $data['numSecu'], $data['numeroTel']);
    }
}