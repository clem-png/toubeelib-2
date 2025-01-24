<?php

namespace toubeelib_rdv\application\AdapterInterface;

interface AdapterBrokerInterface
{
    public function publish($message, $routingKey);
}