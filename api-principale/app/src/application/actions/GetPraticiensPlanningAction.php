<?php

namespace toubeelib\application\actions;

use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;
use Slim\Exception\HttpBadRequestException;
use toubeelib\core\services\rdv\ServiceRDVInterface;

class GetPraticiensPlanningAction extends AbstractAction {

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
                                    ->key('dateFin', Validator::stringType()->notEmpty()->dateTime('Y-m-d H:i'))
                                    ->key('idSpe', Validator::stringType()->notEmpty())
                                    ->key('type', Validator::stringType()->notEmpty());

        try {
            $praticiensDispoValidator->check($params);
        } catch(NestedValidationException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        $dateDeb = DateTime::createFromFormat('Y-m-d H:i', $params['dateDeb']);
        $dateFin = DateTime::createFromFormat('Y-m-d H:i', $params['dateFin']);
        $idSpe = $params['idSpe'];
        $type = $params['type'];
        $planning = $this->serviceRdv->afficherPlanningPraticien($dateDeb, $dateFin, $id, $idSpe, $type);
        
        $planningArray = [];
        foreach ($planning as $rdv) {
            $rdvArray = [
                'id' => $rdv->id,
                'specialite_label' => $rdv->specialite_label,
                'status' => $rdv->status,
                'type' => $rdv->type,
                'links' => [
                    'self' => [
                        'href' => '/rdvs/' . $rdv->id
                    ]
                ]
            ];
            $planningArray[] = $rdvArray;
        }
        
        $response = [
            "type" => "collection",
            "planning" => $planningArray
        ];

        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}