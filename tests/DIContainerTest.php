<?php
namespace Tests;
use DIContainer\DIContainer;
use Tests\dummySrc\Foo;

class DIContainerTest extends \PHPUnit\Framework\TestCase
{
    public function testCanCreateObj()
    {
        $this->assertInstanceOf(
            DIContainer::class,
            new DIContainer()
        );
    }

    public function testCanCreateFoo()
    {
        $dic = new DIContainer();
        $foo = $dic->get(Foo::class);
        $this->assertInstanceOf(
            Foo::class,
            $foo
        );
    }

    // todo add more tests
}