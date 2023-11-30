<?php

namespace Pnl\Test\TestsSuite;

use PHPUnit\Framework\TestCase;
use Pnl\App\Command\WelcomeCommand;
use Pnl\Console\InputResolver;
use Pnl\Console\Input\ArgumentBag;
use Pnl\Console\Input\ArgumentDefinition;
use Pnl\Console\Input\ArgumentType;
use Pnl\Console\Input\Input;
use Pnl\Console\Input\InputBag;

class InputResolverTest extends TestCase
{
    public function testNoInput()
    {
        $resolver = new InputResolver();

        Command::$arguments = new ArgumentBag();

        $this->assertEquals(new InputBag(), $resolver->resolve(new Command(), new InputBag()));
    }

    /**
     * @dataProvider argumentsProvider
     */
    public function testArgumentsOfType(ArgumentType $arg, mixed $value)
    {
        $resolver = new InputResolver();
        $bag = new ArgumentBag();

        $bag->addArgument(new ArgumentDefinition('test', false, null, $arg));

        Command::$arguments = $bag;

        $input = new Input([sprintf('--test=%s', $value)]);

        $expected = new InputBag();
        $expected->addArgument('test', $value);

        $this->assertEquals($expected, $resolver->resolve(new Command(), $input));
    }

    public static function argumentsProvider(): iterable
    {
        yield [ArgumentType::STRING, 'test'];
        yield [ArgumentType::INT, 1];
        yield [ArgumentType::FLOAT, 1.1];
        yield [ArgumentType::BOOLEAN, true];
    }

    public function testArgumentWithDefaultValue()
    {
        $resolver = new InputResolver();
        $bag = new ArgumentBag();

        $bag->addArgument(new ArgumentDefinition('test', false, 'default arg', default: 'default'));

        Command::$arguments = $bag;

        $input = new Input([]);

        $expected = new InputBag();
        $expected->addArgument('test', 'default');

        $this->assertEquals($expected, $resolver->resolve(new Command(), $input));
    }

    public function testArgumentWithDefaultValueAndInput()
    {
        $resolver = new InputResolver();
        $bag = new ArgumentBag();

        $bag->addArgument(new ArgumentDefinition('test', false, 'default arg', default: 'default'));

        Command::$arguments = $bag;

        $input = new Input(['--test=test']);

        $expected = new InputBag();
        $expected->addArgument('test', 'test');

        $this->assertEquals($expected, $resolver->resolve(new Command(), $input));
    }

    public function testInputWithNameless(): void
    {
        $resolver = new InputResolver();
        $bag = new ArgumentBag();

        $bag->addArgument(new ArgumentDefinition('test', false, 'default arg', ArgumentType::STRING, 'default', false));
        $bag->addArgument(new ArgumentDefinition('test2', false, 'default arg', ArgumentType::INT, 12, true));
        $bag->addArgument(new ArgumentDefinition('test3', false, 'default arg', ArgumentType::STRING, '12', false));

        Command::$arguments = $bag;

        $input = new Input(['173']);

        $expected = new InputBag();
        $expected->addArgument('test', 'default');
        $expected->addArgument('test2', 173, true);
        $expected->addArgument('test3', '12');

        $this->assertEquals($expected, $resolver->resolve(new Command(), $input));
    }

    public function testComplexBag(): void
    {
        $resolver = new InputResolver();
        $bag = new ArgumentBag();

        $bag->addArgument(new ArgumentDefinition('test', false, 'default int arg', ArgumentType::INT, default: 2));
        $bag->addArgument(new ArgumentDefinition('test2', false, 'default string arg', ArgumentType::STRING, default: 'default'));
        $bag->addArgument(new ArgumentDefinition('test3', false, 'default boolean arg',  ArgumentType::BOOLEAN, default: false));

        Command::$arguments = $bag;

        $input = new Input(['--test=8', '--test3']);

        $expected = new InputBag();
        $expected->addArgument('test', 8);
        $expected->addArgument('test2', 'default');
        $expected->addArgument('test3', true);

        $this->assertEquals($expected, $resolver->resolve(new Command(), $input));
    }
}

class Command extends WelcomeCommand
{
    public static ArgumentBag $arguments;

    public static function getArguments(): ArgumentBag
    {
        return self::$arguments;
    }
}
