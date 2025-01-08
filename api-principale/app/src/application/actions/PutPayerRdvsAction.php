<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;
use toubeelib\core\services\rdv\RdvServiceException;
use toubeelib\core\services\rdv\ServiceRDVInterface;

class PutPayerRdvsAction extends AbstractAction
{

    private ServiceRDVInterface $serviceRDV;

    public function __construct(ServiceRDVInterface $serviceRDV)
    {
        $this->serviceRDV = $serviceRDV;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $id = $args['ID-RDV'];

        try{
            $rdv = $this->serviceRDV->payerRdv($id);
        }catch (RdvServiceException $e){
            throw new HttpUnauthorizedException($rq, 'Impossible de payer le rendez-vous, il n\'a pas été honoré');
        }

        $response = [
            "type"=> "ressource",
            "rdv" => $rdv,
            "payer" => [
                "href" => "/rdvs/$id/payer",
            ],
            "annuler" => [
                "href" => "/rdvs/$id/annuler",
            ],
            "modifier" => [
                "href" => "/rdvs/$id/modifier",
            ]
        ];

        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}