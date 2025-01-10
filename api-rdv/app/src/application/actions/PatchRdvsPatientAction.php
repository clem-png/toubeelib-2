<?php

namespace toubeelib\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;
use Slim\Exception\HttpBadRequestException;
use toubeelib\core\dto\InputSpecialiteDTO;
use toubeelib\core\services\rdv\ServiceRDVInterface;

class PatchRdvsPatientAction extends AbstractAction
{

    private ServiceRDVInterface $serviceRdv;

    public function __construct(ServiceRDVInterface $serviceRdv)
    {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $id = $args['ID-RDV'];
        $params = $rq->getParsedBody();
        if (empty($params['idPatient']) && empty($params['specialite'])) {
            throw new HttpBadRequestException($rq, 'Aucune valeur fournit');
        }
       if (isset($params['idPatient']) && isset($params['specialite'])) {
           $type = 0;
       } elseif (isset($params['idPatient'])) {
           $type = 1;
       } else {
           $type = 2;
       }
        $validator = null;
        switch ($type) {
            case 0:
                $validator = Validator::key('idPatient', Validator::stringType()->notEmpty())
                    ->key('specialite', Validator::stringType()->notEmpty());
                break;
            case 1:
                $validator = Validator::key('idPatient', Validator::stringType()->notEmpty());
                break;
            case 2:
                $validator = Validator::key('specialite', Validator::stringType()->notEmpty());
                break;
        }
        try {
            $validator->check($params);
        } catch (Exception $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }
        if (isset($params['idPatient']) && isset($params['specialite'])) {
            if ((filter_var($params['idPatient'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['idPatient'] || filter_var($params['specialite'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['specialite'])) {
                throw new HttpBadRequestException($rq, 'Mauvais format de données');
            }
        }elseif (isset($params['idPatient'])) {
            if (filter_var($params['idPatient'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['idPatient']) {
                throw new HttpBadRequestException($rq, 'Mauvais format de données');
            }
        }else {
            if (filter_var($params['specialite'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['specialite']) {
                throw new HttpBadRequestException($rq, 'Mauvais format de données');
            }
        }

        $res = null;
        switch ($type) {
            case 0:
                $res = $this->serviceRdv->modifierPatientOuSpecialiteRdv($id, $params['idPatient'], new InputSpecialiteDTO($params['specialite']));
                break;
            case 1:
                $res = $this->serviceRdv->modifierPatientOuSpecialiteRdv($id, $params['idPatient']);
                break;
            case 2:
                $res = $this->serviceRdv->modifierPatientOuSpecialiteRdv($id, null, new InputSpecialiteDTO($params['specialite']));
                break;
        }
        $rs->getBody()->write(json_encode($res));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}