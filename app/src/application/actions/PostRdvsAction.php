<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;
use Slim\Exception\HttpBadRequestException;
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
        $params = $rq->getParsedBody() ?? null;
        $rdvsInputValidator =
            Validator::key('idPatient', Validator::stringType()->notEmpty())
                ->key('idPraticien', Validator::stringType()->notEmpty())
                ->key('date', Validator::stringType()->notEmpty()->dateTime('Y-m-d H:i'))
                ->key('specialite', Validator::optional(Validator::stringType()->notEmpty()))
                ->key('status', Validator::stringType()->notEmpty());

        try {
            $rdvsInputValidator->check($params);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        if ((filter_var($params['idPatient'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['idPatient'] || filter_var($params['idPraticien'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['idPraticien'] || filter_var($params['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['status'] || filter_var($params['specialite'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $params['specialite'])) {
            throw new HttpBadRequestException($rq, 'Mauvais format de données');
        }

        $rdvDTO = new InputRdvDTO($params['idPraticien'], $params['idPatient'], new \DateTimeImmutable($params['date']), $params['status'], $params['specialite'] ?? null);
        try {
            $res = $this->serviceRdv->creerRdv($rdvDTO);
        }
        catch (\Exception $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        $res = $res->jsonSerialize();
        $response = [
            "type" => "resource",
            "rdv" => $res
        ];
        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Location', "/rdvs/{$res['id']}")->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}