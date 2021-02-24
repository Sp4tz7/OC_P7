<?php

namespace App\Listener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Response;

class ExceptionListener
{


    public function onKernelException(ExceptionEvent $event)
    {
        $throwable = $event->getThrowable();
        $type = get_class($throwable);

        switch ($type) {
            case 'Symfony\Component\Routing\Exception\ResourceNotFoundException':
                $statusCode = Response::HTTP_NOT_FOUND;
                break;

            case 'Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException':
                $statusCode = Response::HTTP_NOT_IMPLEMENTED;
                break;

            case 'App\Exception\ApiForbiddenException':
                $statusCode = Response::HTTP_FORBIDDEN;
                break;

            case 'App\Exception\ApiValidationException':
                $statusCode = Response::HTTP_BAD_REQUEST;
                break;

            default:
                $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                break;

        }

        if (method_exists($throwable, 'getStatusCode')) {
            $statusCode = $throwable->getStatusCode();
        }

        $error = [];

        $message = $throwable->getMessage();
        if (is_string($message)) {
            $error['detail'] = $message;
        } else {
            $error = $message;
        }

        $error['code'] = $statusCode;

        $content = $error;

        $response = new Response();
        $response->setStatusCode($statusCode);
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = json_encode($content);

        if (json_last_error() != JSON_ERROR_NONE) {
            $jsonContent = "['error converting to json: ".json_last_error_msg()."']";
        }
        $response->setContent($jsonContent);
        $event->setResponse($response);
    }

}
