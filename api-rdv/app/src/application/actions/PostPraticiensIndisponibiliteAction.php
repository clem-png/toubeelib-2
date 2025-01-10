<?php

namespace toubeelib_rdv\application\actions;

use DateMalformedStringException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Exception\HttpBadRequestException;
use DateTime;
use toubeelib_rdv\core\services\rdv\ServiceRDVInterface;

class PostPraticiensIndisponibiliteAction extends AbstractAction
{
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

        
        try {
            $this->serviceRdv->indisponibilitePraticien(new DateTime(datetime: $params['dateDeb']), new DateTime($params['dateFin']), $id);
        }
        catch (Exception $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        return $rs->withStatus(201);
    }
}