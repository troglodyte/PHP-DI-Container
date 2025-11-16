<?php

namespace Tests\dummySrc;

class Baz
{
    public function __construct(
        protected Bar $bar,
    )
    {

    }
}