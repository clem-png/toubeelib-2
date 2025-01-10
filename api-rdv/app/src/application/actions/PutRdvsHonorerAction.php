<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpForbiddenException;
use toubeelib\core\services\rdv\ServiceRDVInterface;

class PutRdvsHonorerAction extends AbstractAction
{

    private ServiceRDVInterface $serviceRDV;

    public function __construct(ServiceRDVInterface $serviceRDV)
    {
        $this->serviceRDV = $serviceRDV;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $id = $args['ID-RDV'];

        try {
            $serviceRDV = $this->serviceRDV->marquerRdvHonore($id);
        }catch (\Exception $e){
            throw new HttpForbiddenException($rq, "impossible d'honorer le rendez-vous");
        }

        $response = [
            "type"=> "ressource",
            "rdv" => $serviceRDV,
        ];

        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}