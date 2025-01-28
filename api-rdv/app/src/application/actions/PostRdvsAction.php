<?php

namespace toubeelib_rdv\application\actions;

use DateMalformedStringException;
use DateTimeImmutable;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;
use Slim\Exception\HttpBadRequestException;
use toubeelib_rdv\application\AdapterInterface\AdapterBrokerInterface;
use toubeelib_rdv\core\dto\InputRdvDTO;
use toubeelib_rdv\core\dto\InputSpecialiteDTO;
use toubeelib_rdv\core\services\rdv\ServiceRDVInterface;

class PostRdvsAction extends AbstractAction
{
    private ServiceRDVInterface $serviceRdv;
    private AdapterBrokerInterface $adapterBroker;

    public function __construct(ServiceRDVInterface $serviceRdv, AdapterBrokerInterface $adapterBroker)
    {
        $this->serviceRdv = $serviceRdv;
        $this->adapterBroker = $adapterBroker;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $params = $rq->getParsedBody() ?? null;
        $rdvsInputValidator =
            Validator::key('idPatient', Validator::stringType()->notEmpty())
                ->key('idPraticien', Validator::stringType()->notEmpty())
                ->key('date', Validator::stringType()->notEmpty()->dateTime('Y-m-d H:i'))
                ->key('specialite', Validator::optional(Validator::stringType()->notEmpty()))
            ->key('type', Validator::stringType()->notEmpty());

        try {
            $rdvsInputValidator->check($params);
        } catch (Exception $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        if ((filter_var($params['idPatient'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['idPatient']
                || filter_var($params['idPraticien'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['idPraticien']
                || filter_var($params['specialite'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['specialite'])
                || filter_var($params['type'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['type']) {
            throw new HttpBadRequestException($rq, 'Mauvais format de données');
        }

        $rdvDTO = new InputRdvDTO($params['idPraticien'], $params['idPatient'], new DateTimeImmutable($params['date']), $params['type'], new InputSpecialiteDTO($params['specialite']) ?? null);
        try {
            $res = $this->serviceRdv->creerRdv($rdvDTO);
        }
        catch (Exception $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        $res = $res->jsonSerialize();
        $response = [
            "type" => "resource",
            "rdv" => $res
        ];

        try {
            $message = $this->serviceRdv->getCreateRDVMessage($res['idPraticien'], $res['idPatient'], $res);
        }catch (Exception $e){
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        $this->adapterBroker->publish($message, 'rdv');

        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Location', "/rdvs/{$res['id']}")->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}