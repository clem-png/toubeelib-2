<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use toubeelib\core\services\rdv\RdvServiceException;
use toubeelib\core\services\rdv\ServiceRDVInterface;


class GetRdvsByIdAction extends AbstractAction
{

    private ServiceRDVInterface $serviceRdv;

    public function __construct(ServiceRDVInterface $serviceRdv)
    {
        $this->serviceRdv = $serviceRdv;
    }



    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $id = $args['ID-RDV'];
        try {
            $rdv = $this->serviceRdv->consulterRdv($id);
        }
        catch (RdvServiceException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }
        $response = [
            "type"=> "ressource",
            "rdv" => $rdv
        ];
        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}