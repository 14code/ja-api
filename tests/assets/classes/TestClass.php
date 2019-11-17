<?php
declare(strict_types=1);

namespace Tests\Assets\Classes;

class TestClass
{
    private $value1;
    private $value2;

    public function __construct($parameter1, $parameter2)
    {
        $this->value1 = $parameter1;
        $this->value2 = $parameter2;
    }

/*
    public function __invoke($arg1, $arg2)
    {
        return [
            $this->value1 . $arg1,
            $this->value2 . $arg2
        ];
    }
*/

}