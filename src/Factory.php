<?php
declare(strict_types=1);

namespace I4code\JaApi;

interface Factory
{
    public function createFromArray(array $data);
}