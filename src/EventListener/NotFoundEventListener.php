<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsEventListener]
class NotFoundEventListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $error = $event->getThrowable();
        if (!$error instanceof NotFoundHttpException) {
            return;
        }

        $request = $event->getRequest();
        $language = $request->getPreferredLanguage();

        if (!str_starts_with($request->getPathInfo(), "/$language")) {
            $response = new Response(status: 302);
            $response->headers->add([
                'Location' => "/$language" . $request->getPathInfo()
            ]);

            $event->setResponse($response);
        }

    }
}