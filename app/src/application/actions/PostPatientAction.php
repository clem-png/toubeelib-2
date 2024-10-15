<?php

namespace toubeelib\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use toubeelib\core\services\patient\ServicePatientInterface;

class PostPatientAction extends AbstractAction
{
    private ServicePatientInterface $servicePatient;

    public function __construct(ServicePatientInterface $servicePatient)
    {
        $this->servicePatient = $servicePatient;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $params = $rq->getParsedBody() ?? null;
        $patientInputValidator =
            Validator::key('nom', Validator::stringType()->notEmpty())
                ->key('prenom', Validator::stringType()->notEmpty())
                ->key('dateNaissance', Validator::stringType()->notEmpty()->dateTime('Y-m-d'))
                ->key('adresse', Validator::stringType()->notEmpty())
                ->key('telephone', Validator::stringType()->notEmpty())
                ->key('email', Validator::stringType()->notEmpty()->email())
                ->key('password', Validator::stringType()->notEmpty());

        try {
            $patientInputValidator->check($params);
        } catch (Exception $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        if ((filter_var($params['nom'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['nom']
            || filter_var($params['prenom'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['prenom']
            || filter_var($params['adresse'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['adresse']
            || filter_var($params['telephone'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['telephone']
            || filter_var($params['email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['email']
            || filter_var($params['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['password'])) {
            throw new HttpBadRequestException($rq, 'Mauvais format de données');
        }
        return $rs;
    }
}