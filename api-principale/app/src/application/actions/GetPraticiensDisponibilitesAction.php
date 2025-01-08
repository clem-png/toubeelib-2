<?php

namespace toubeelib\application\actions;

use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;
use Slim\Exception\HttpBadRequestException;
use toubeelib\core\services\rdv\ServiceRDVInterface;

class GetPraticiensDisponibilitesAction  extends AbstractAction {

    private ServiceRDVInterface $serviceRdv;

    public function __construct(ServiceRDVInterface $serviceRdv)
    {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $id = $args['ID-PRATICIEN'];
        $params = $rq->getParsedBody() ?? null;
        $praticiensDispoValidator = Validator::key('dateDeb', Validator::stringType()->notEmpty()->dateTime('Y-m-d H:i'))
                                    ->key('dateFin', Validator::stringType()->notEmpty()->dateTime('Y-m-d H:i'));

        try {
            $praticiensDispoValidator->check($params);
        } catch(NestedValidationException $e) {
            throw new HttpBadRequestException($rq,$e->getMessage());
        }

        $dateDeb = DateTime::createFromFormat('Y-m-d H:i', $params['dateDeb']);
        $dateFin = DateTime::createFromFormat('Y-m-d H:i', $params['dateFin']);
        $disponibilites = $this->serviceRdv->listerDisponibilitePraticien($dateDeb, $dateFin, $id);
        $response = [
            "type"=> "collection",
            "disponibilites" => $disponibilites
        ];
        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}