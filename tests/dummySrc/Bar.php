<?php

namespace Tests\dummySrc;

class Bar
{
    public function __construct(
        protected Qux $qux,
        protected Quux $quux,
    )
    {

    }
}