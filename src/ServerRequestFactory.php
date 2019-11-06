<?php
declare(strict_types=1);

namespace I4code\JaApi;

use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;


class ServerRequestFactory
{
    protected $psr17Factory;


    public function __construct()
    {
        $this->psr17Factory = new Psr17Factory();
    }


    /**
     * @param string $method
     * @param string $uri
     * @return ServerRequestInterface
     */
    public function createTestRequest(string $method, string $uri): ServerRequestInterface
    {
        $serverRequest = $this->psr17Factory->createServerRequest($method, $uri);

        return $serverRequest;
    }


    /**
     * @return ServerRequestInterface
     */
    public function createLiveRequest(): ServerRequestInterface
    {
        $creator = new ServerRequestCreator(
            $this->psr17Factory, // ServerRequestFactory
            $this->psr17Factory, // UriFactory
            $this->psr17Factory, // UploadedFileFactory
            $this->psr17Factory  // StreamFactory
        );

        $serverRequest = $creator->fromGlobals();

        return $serverRequest;
    }

}