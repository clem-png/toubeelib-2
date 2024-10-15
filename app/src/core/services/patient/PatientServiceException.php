<?php

namespace toubeelib\core\services\patient;

use Exception;

class PatientServiceException
{

    /**
     * @param Exception $e
     */
    public function __construct(\Exception $e)
    {
    }
}