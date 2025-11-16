<?php

namespace Tests\dummySrc;

class Foo
{
    public function __construct(
        protected Bar $bar,
        protected Baz $baz,
    )
    {

    }
}