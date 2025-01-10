<?php

namespace toubeelib_praticiens\application\actions;

use DI\NotFoundException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use toubeelib_praticiens\core\services\praticien\ServicePraticienInterface;

class GetPraticiensByIdAction extends AbstractAction
{

    private ServicePraticienInterface $servicePraticien;

    public function __construct(ServicePraticienInterface $servicePraticien)
    {
        $this->servicePraticien = $servicePraticien;
    }

    /**
     * @throws NotFoundException
     */
    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {

        try {
            $id = $args['ID-PRATICIEN'];
            $praticien = $this->servicePraticien->getPraticienById($id);
            $response = [
                "type" => "ressource",
                "praticien" => $praticien,
            ];
            $rs->getBody()->write(json_encode($response));
        } catch (Exception $e) {
            throw new HttpNotFoundException($rq,"le praticien n'existe pas !");
        }
        return $rs->withHeader('Content-Type', 'application/json');
    }
}