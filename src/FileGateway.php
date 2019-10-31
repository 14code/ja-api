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


    public function getSrcString()
    {
        $srcString = file_get_contents($this->srcFile);
        return $srcString;
    }


    public function putSrcString(string $string)
    {
        file_put_contents($this->srcFile, $string);
    }


    public function getDecoded()
    {
        return $this->encoder->decode($this->getSrcString());
    }


    public function getEncoded(array $data)
    {
        return $this->encoder->encode($data);
    }


    abstract public function retrieveAll(): array;
    abstract public function persist(array $data);

}