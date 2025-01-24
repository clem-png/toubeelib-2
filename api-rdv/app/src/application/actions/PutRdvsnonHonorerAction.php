<?php

namespace toubeelib_rdv\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpForbiddenException;
use toubeelib_rdv\core\services\rdv\ServiceRDVInterface;

class PutRdvsnonHonorerAction extends AbstractAction
{

    private ServiceRDVInterface $serviceRDV;

    public function __construct(ServiceRDVInterface $serviceRDV)
    {
        $this->serviceRDV = $serviceRDV;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $id = $args['id'];

        try {
            $serviceRDV = $this->serviceRDV->marquerRdvNonHonore($id);
        } catch (\Exception $e) {
            throw new HttpForbiddenException($rq, "impossible de marquer le rendez-vous comme non honoré");
        }
        $response = [
            "type"=> "ressource",
            "rdv" => $serviceRDV,
        ];

        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}