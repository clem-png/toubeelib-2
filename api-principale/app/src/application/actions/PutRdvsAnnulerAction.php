<?php

namespace toubeelib\application\actions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRDVInterface;

class PutRdvsAnnulerAction extends AbstractAction
{
    private ServiceRDVInterface $serviceRdv;

    public function __construct(ServiceRDVInterface $serviceRdv)
    {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $id = $args['ID-RDV'];
        $rdv = $this->serviceRdv->annulerRdv($id);

        $response = [
            "type"=> "ressource",
            "rdv" => $rdv,
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