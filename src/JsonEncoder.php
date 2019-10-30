<?php
declare(strict_types=1);

namespace I4code\JaApi;


class JsonEncoder implements Encoder
{
    protected $encoder;

    public function __construct()
    {
        $this->encoder = new \Symfony\Component\Serializer\Encoder\JsonEncoder();
    }

    public function encode(array $data): string
    {
        $encoded = '';
        return $encoded;
    }


    public function decode(string $data): array
    {
        return $this->encoder->decode($data, 'json');
    }

}