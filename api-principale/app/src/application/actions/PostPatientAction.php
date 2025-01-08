<?php

namespace toubeelib\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\ComponentException;
use Respect\Validation\Rules\Date;
use Respect\Validation\Validator;
use Slim\Exception\HttpBadRequestException;
use toubeelib\core\dto\InputPatientDTO;
use toubeelib\core\services\patient\ServicePatientInterface;

class PostPatientAction extends AbstractAction
{
    private ServicePatientInterface $servicePatient;

    public function __construct(ServicePatientInterface $servicePatient)
    {
        $this->servicePatient = $servicePatient;
    }

    /**
     * @throws ComponentException
     */
    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $params = $rq->getParsedBody() ?? null;
        $patientInputValidator =
            Validator::key('nom', Validator::stringType()->notEmpty())
                ->key('prenom', Validator::stringType()->notEmpty())
                ->key('dateNaissance', Validator::stringType()->notEmpty()->dateTime('Y-m-d'))
                ->key('adresse', Validator::stringType()->notEmpty())
                ->key('telephone', Validator::stringType()->notEmpty())
                ->key('mail', Validator::stringType()->notEmpty()->email())
                ->key('password', Validator::stringType()->notEmpty());

        try {
            $patientInputValidator->check($params);
        } catch (Exception $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        if (filter_var($params['nom'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['nom']) {
            throw new HttpBadRequestException($rq, 'Mauvais format de données pour le nom');
        }
        if (filter_var($params['prenom'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['prenom']) {
            throw new HttpBadRequestException($rq, 'Mauvais format de données pour le prénom');
        }
        if (filter_var($params['adresse'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['adresse']) {
            throw new HttpBadRequestException($rq, 'Mauvais format de données pour l\'adresse');
        }
        if (filter_var($params['telephone'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['telephone']) {
            throw new HttpBadRequestException($rq, 'Mauvais format de données pour le téléphone');
        }
        if (filter_var($params['mail'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['mail']) {
            throw new HttpBadRequestException($rq, 'Mauvais format de données pour le mail');
        }
        if (filter_var($params['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['password']) {
            throw new HttpBadRequestException($rq, 'Mauvais format de données pour le mot de passe');
        }
        $patient = new InputPatientDTO($params['nom'],
            $params['prenom'],
            $params['adresse'],
            $params['telephone'],
            $params['mail'],
            $params['dateNaissance'],
            $params['password']
            );
        try {
            $res = $this->servicePatient->creerPatient($patient);
        } catch (Exception $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }
        $res = $res->jsonSerialize();
        $response = [
            "type" => "resource",
            "patient" => $res
        ];
        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}