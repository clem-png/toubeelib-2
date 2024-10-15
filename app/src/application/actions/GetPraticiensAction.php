<?php

namespace toubeelib\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;
use Slim\Exception\HttpBadRequestException;
use toubeelib\core\dto\InputSearchDTO;
use toubeelib\core\services\praticien\ServicePraticienInterface;

class GetPraticiensAction extends AbstractAction
{

    private ServicePraticienInterface $servicePraticien;

    public function __construct(ServicePraticienInterface $servicePraticien)
    {
        $this->servicePraticien = $servicePraticien;
    }
    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $params = $rq->getParsedBody() ?? null; // prenom, nom, tel, adresse

        if(isset($params['prenom'])){
            $validatorPrenom = Validator::stringType()->notEmpty();
            try {
                $validatorPrenom->check($params['prenom']);
            } catch(Exception $e) {
                throw new HttpBadRequestException($rq,$e->getMessage());
            }
            if (filter_var($params['prenom'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['prenom']) {
                throw new HttpBadRequestException($rq, 'Mauvais format de données pour le prénom');
            }
        }else {
            $params['prenom'] = null;
        }

        if (isset($params['nom'])){
            $validatorNom = Validator::stringType()->notEmpty();
            try {
                $validatorNom->check($params['nom']);
            } catch(Exception $e) {
                throw new HttpBadRequestException($rq,$e->getMessage());
            }
            if (filter_var($params['nom'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['nom']) {
                throw new HttpBadRequestException($rq, 'Mauvais format de données pour le nom');
            }
        }else{
            $params['nom'] = null;
        }

        if (isset($params['tel'])){
            $validatorTel = Validator::stringType()->notEmpty();
            try {
                $validatorTel->check($params['tel']);
            } catch(Exception $e) {
                throw new HttpBadRequestException($rq,$e->getMessage());
            }
            if (filter_var($params['tel'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['tel']) {
                throw new HttpBadRequestException($rq, 'Mauvais format de données pour le téléphone');
            }
        }else{
            $params['tel'] = null;
        }

        if (isset($params['adresse'])){
            $validatorAdresse = Validator::stringType()->notEmpty();
            try {
                $validatorAdresse->check($params['adresse']);
            } catch(Exception $e) {
                throw new HttpBadRequestException($rq,$e->getMessage());
            }
            if (filter_var($params['adresse'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['adresse']) {
                throw new HttpBadRequestException($rq, 'Mauvais format de données pour l\'adresse');
            }
        }else{
            $params['adresse'] = null;
        }

        $search = new InputSearchDTO($params['nom'],$params['prenom'],$params['adresse'],$params['tel']);
        try{
            $res = $this->servicePraticien->searchPraticiens($search);
        } catch (Exception $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        $response = [
            "type" => "collection",
            "praticiens" => []
        ];

        //pour chaque objet de $res transformé et ajouter à $response
        foreach ($res as $praticien) {
            $response['praticiens'][] = [
                "type" => "resource",
                "praticien" => [
                    "id" => $praticien->ID,
                    "nom" => $praticien->nom,
                    "prenom" => $praticien->prenom,
                    "adresse" => $praticien->adresse,
                    "tel" => $praticien->tel,
                    "specialite" => $praticien->specialite_label
                ]
            ];
        }


        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Content-Type', 'application/json')->withStatus(200);

    }
}