<?php

namespace toubeelib_praticiens\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;
use Slim\Exception\HttpBadRequestException;
use toubeelib_praticiens\core\dto\InputPraticienDTO;
use toubeelib_praticiens\core\dto\InputSpecialiteDTO;
use toubeelib_praticiens\core\services\praticien\ServicePraticienInterface;

class PostPraticiensAction extends AbstractAction
{

    private ServicePraticienInterface $servicePraticien;

    public function __construct(ServicePraticienInterface $servicePraticien)
    {
        $this->servicePraticien = $servicePraticien;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $params = $rq->getParsedBody() ?? null; // prenom, nom, tel, adresse, specialite

        if (!isset($params['nom']) || !isset($params['prenom']) || !isset($params['adresse']) || !isset($params['tel'])) {
            throw new HttpBadRequestException($rq, 'Paramètres manquants');
        }

        $validatorPraticien = Validator::arrayType()->key('nom', Validator::stringType()->notEmpty())
            ->key('prenom', Validator::stringType()->notEmpty())
            ->key('adresse', Validator::stringType()->notEmpty())
            ->key('tel', Validator::stringType()->notEmpty());

        try {
            $validatorPraticien->check($params);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        if (isset($params['specialite'])) {
            $validatorSpecialite = Validator::stringType()->notEmpty();
            try {
                $validatorSpecialite->check($params['specialite']);
                if (filter_var($params['specialite'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['specialite']) {
                    throw new HttpBadRequestException($rq, 'Mauvais format de données pour la spécialité');
                }
            } catch (\Exception $e) {
                throw new HttpBadRequestException($rq, $e->getMessage());
            }
        }

        //filter var des params
        if (filter_var($params['nom'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['nom']
            || filter_var($params['prenom'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['prenom']
            || filter_var($params['adresse'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['adresse']
            || filter_var($params['tel'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['tel']) {
            throw new HttpBadRequestException($rq, 'Mauvais format de données pour le nom');
        }

        $spe = new InputSpecialiteDTO($params['specialite']);
        $praticien = new InputPraticienDTO($params['nom'], $params['prenom'], $params['adresse'], $params['tel'], $spe);

        try {
            $res = $this->servicePraticien->createPraticien($praticien);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        $res = [
            "id" => $res->ID,
            "nom" => $res->nom,
            "prenom" => $res->prenom,
            "adresse" => $res->adresse,
            "tel" => $res->tel,
            "specialite" => $res->specialite_label
        ];

        $response = [
            "type" => "ressource",
            "praticien" => $res
        ];

        $rs->getBody()->write(json_encode($response));

        return $rs->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}