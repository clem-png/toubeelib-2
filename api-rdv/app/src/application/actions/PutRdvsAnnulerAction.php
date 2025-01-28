<?php

namespace toubeelib_rdv\application\actions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib_rdv\core\services\rdv\ServiceRDVInterface;
use toubeelib_rdv\application\AdapterInterface\AdapterBrokerInterface;

class PutRdvsAnnulerAction extends AbstractAction
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
        $id = $args['id'];
        $rdv = $this->serviceRdv->annulerRdv($id);

        $response = [
            "type"=> "ressource",
            "rdv" => $rdv,
            "annuler" => [
                "href" => "/rdvs/$id/annuler",
            ],
            "modifier" => [
                "href" => "/rdvs/$id/modifier",
            ]
        ];

        $res = $rdv->jsonSerialize();

        try {
            $message = $this->serviceRdv->createMessage($res, "annuler");
        }catch (Exception $e){
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        $this->adapterBroker->publish($message, 'rdv');

        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}