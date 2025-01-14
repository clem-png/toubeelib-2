<?php

namespace toubeelib_praticiens\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use toubeelib_praticiens\core\services\praticien\ServicePraticienInterface;

class GetSpecialiteByIdAction extends AbstractAction
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
            $id = $args['ID-SPECIALITE'];
            $specialite = $this->servicePraticien->getSpecialiteById($id);
            $response = [
                "type" => "ressource",
                "specialite" => $specialite,
            ];
            $rs->getBody()->write(json_encode($response));
        } catch (Exception $e) {
            throw new HttpNotFoundException($rq,"la spécialité n'existe pas !");
        }
        return $rs->withHeader('Content-Type', 'application/json');
    }
}