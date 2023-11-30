<?php

namespace Pnl\Test\TestsSuite;

use PHPUnit\Framework\TestCase;
use Pnl\Console\Input\Input;

class InputTest extends TestCase
{
    public function testNoInput()
    {
        $this->assertTrue(true);
    }

    /**
     * @dataProvider inputProvider
     */
    public function testInput(array $inputs, array $expected, mixed $nameless = null): void
    {
        $input = new Input($inputs);

        if ($nameless !== null) {
            $this->assertEquals($nameless, $input->getNameless());
        }

        $this->assertEquals($expected, $input->getAllArguments());
    }

    public static function inputProvider(): iterable
    {
        yield [
            [],
            [],
        ];

        yield [
            ['--test=1'],
            ['test' => '1'],
        ];

        yield [
            ['--test=1', '--test2=2'],
            ['test' => '1', 'test2' => '2'],
        ];

        yield [
            [1, '--test2=2'],
            ['test2' => '2'],
            1
        ];
    }
}
