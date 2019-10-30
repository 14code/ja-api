<?php
declare(strict_types=1);

namespace I4code\JaApi;

use I4code\JaApi\Exceptions\NoDatasourceException;

abstract class FileGateway implements Gateway
{
    protected $srcFile;
    protected $encoder;


    public function __construct(string $srcFile, Encoder $encoder)
    {
        if (empty($srcFile) || !file_exists($srcFile)) {
            throw new NoDatasourceException(static::ERROR_NO_DATASOURCE);
        }

        $this->srcFile = $srcFile;
        $this->encoder = $encoder;
    }


    public function getSrcString(): string
    {
        $srcString = file_get_contents($this->srcFile);
        return $srcString;
    }


    public function getDecoded(): array
    {
        $decoded = $this->encoder->decode($this->getSrcString());
        return $decoded;
    }


    abstract public function retrieveAll(): array;

}