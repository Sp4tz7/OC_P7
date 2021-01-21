<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiException
{

    private $error;
    private $error_description;
    private $error_url;
    private $status;

    public function __construct($status){
        $this->status = $status;
    }

    /**
     * @param mixed $error
     */
    public function setError($error): void
    {
        $this->error = $error;
    }

    /**
     * @param mixed $error_description
     */
    public function setErrorDescription($error_description): void
    {
        $this->error_description = $error_description;
    }

    /**
     * @param mixed $error_url
     */
    public function setErrorUrl($error_url): void
    {
        $this->error_url = $error_url;
    }


    public function getException(): JsonResponse
    {
        $message = [
            'error' => $this->error,
            'error_description' => $this->error_description,
            'retailer_url' => $this->error_url,
        ];
        return new JsonResponse($message, $this->status);
    }

}