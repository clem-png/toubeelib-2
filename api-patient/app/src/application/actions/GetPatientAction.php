<?php

namespace toubeelib_patient\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\ComponentException;
use Respect\Validation\Validator;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use toubeelib_patient\core\dto\InputPatientDTO;
use toubeelib_patient\core\services\patient\ServicePatientInterface;

class GetPatientAction extends AbstractAction
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
      try {
        $id = $args['ID-PATIENT'];
        $patient = $this->servicePatient->getPatientById($id);
        $response = [
          "type" => "ressource",
          "patient" => $patient,
        ];
        $rs->getBody()->write(json_encode($response));
      } catch (Exception $e) {
        throw new HttpNotFoundException($rq,"le patient n'existe pas !");
      }
      return $rs->withHeader('Content-Type', 'application/json');
    }
}