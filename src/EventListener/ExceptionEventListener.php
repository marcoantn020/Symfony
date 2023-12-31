<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener]
class ExceptionEventListener
{
    public function __invoke(ExceptionEvent $event)
    {
        return;
            $exception = $event->getThrowable();
            $message = 'Ocorreu um erro tente mais tarde.';

            $response = new Response(
                headers: ['Content-Type' => 'application/json']
            );

            $response->setContent($message);

            if ($exception instanceof HttpExceptionInterface) {
                $response->setStatusCode($exception->getStatusCode());
                $response->headers->replace($exception->getHeaders());
            } else {
                $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $event->setResponse($response);

    }
}