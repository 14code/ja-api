<?php
declare(strict_types=1);

namespace I4code\JaApi\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class EnforceJsonMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        //$requestHandler = $request->getAttribute($this->handlerAttribute);

        if ('application/json' != $response->getHeader('Content-Type')) {
            $response = $response->withHeader("Content-Type", "application/json");
        }

        $bodyStream = $response->getBody();

        $errors = [];

        if (404 == $response->getStatusCode()) {
            $errors[] = (object)[
                'code' => 100,
                'http-status' => 404,
                'message' => 'Ressource not found'
            ];
        }

        $body = (string)$bodyStream;

        if (empty($body)) {
            $errors[] = (object)[
                'code' => 201,
                'http-status' => 505,
                'message' => 'Empty JSON result'
            ];
        } else {
            json_decode((string)$response->getBody());
            if (0 !== json_last_error()) {
                $errors[] = (object)[
                    'code' => 202,
                    'http-status' => 506,
                    'message' => 'Invalid JSON result: ' . json_last_error()
                        . ' / ' . json_last_error_msg()
                ];
            }
        }

        if (0 < count($errors)) {
            $bodyStream->rewind();
            $bodyStream->write(json_encode((object)[
                'errors' => $errors
            ]));
        }

        //$body = (string) $response->getBody();
        //fwrite(STDERR, 'body:' . "\n");
        //fwrite(STDERR, print_r($body, true));

        return $response;

    }

}