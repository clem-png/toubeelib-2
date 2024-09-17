<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\services\rdv\ServiceRDVInterface;

class PostRdvsAction extends AbstractAction
{

    private ServiceRDVInterface $serviceRdv;

    public function __construct(ServiceRDVInterface $serviceRdv)
    {
        $this->serviceRdv = $serviceRdv;
    }


    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $params = $rq->getQueryParams();
        $params['dateDebut'] = new \DateTimeImmutable($params['dateDebut']);
        $input_dto = new InputRdvDTO($params['idPraticien'], $params['idPatient'], $params['dateDebut'], $params['status']);
        $rdv = $this->serviceRdv->creerRdv($input_dto);
        $rs->getBody()->write(json_encode($rdv));
        return $rs->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}