<?php
namespace I4code\JaApi;

final class CorsConfig
{
    public function __construct(
        public readonly array $allowedOrigins = [],
        public readonly array $allowedMethods = ['GET','OPTIONS'],
        public readonly array $allowedHeaders = [],
        public bool $allowAnyOrigin = false, // means "*"
        public readonly bool $allowCredentials = false,
        public readonly int $maxAge = 0,
    ) {}

}
