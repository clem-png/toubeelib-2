<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use toubeelib\core\services\praticien\ServicePraticienInterface;

class GetPraticiensByIdAction extends AbstractAction
{

    private ServicePraticienInterface $servicePraticien;

    public function __construct(ServicePraticienInterface $servicePraticien)
    {
        $this->servicePraticien = $servicePraticien;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $id = $args['ID-PRATICIEN'];
        try {
            $praticien = $this->servicePraticien->getPraticienById($id);
        }catch (\Exception $e){
            throw new HttpNotFoundException($rq, "Praticien not found");
        }

        $response = [
            "type"=> "ressource",
            "praticien" => $praticien,
        ];
        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}