<?php

namespace Pnl\Test\TestsSuite;

use PHPUnit\Framework\TestCase;
use Pnl\Console\Output\ANSI\BackgroundColor;
use Pnl\Console\Output\ANSI\Style as AnsiStyle;
use Pnl\Console\Output\ANSI\TextColors;
use Pnl\Console\Output\ConsoleOutput;
use Pnl\Console\Output\Style\Style;

class OutputTest extends TestCase
{
    /**
     * @dataProvider colorProvider
     */
    public function testTextColor(TextColors $textColors, string $expected)
    {
        $output = new TestStyle(new Output());

        $output->setColor($textColors);

        $output->write("Hello World");

        $this->assertEquals("\033[0;{$expected};49mHello World", $output->getText());
    }

    public static function colorProvider(): iterable
    {
        yield [TextColors::BLACK, TextColors::BLACK->value];
        yield [TextColors::RED, TextColors::RED->value];
        yield [TextColors::GREEN, TextColors::GREEN->value];
        yield [TextColors::YELLOW, TextColors::YELLOW->value];
        yield [TextColors::BLUE, TextColors::BLUE->value];
        yield [TextColors::MAGENTA, TextColors::MAGENTA->value];
        yield [TextColors::CYAN, TextColors::CYAN->value];
        yield [TextColors::WHITE, TextColors::WHITE->value];
        yield [TextColors::DEFAULT, TextColors::DEFAULT->value];
    }

    /**
     * @dataProvider backgroundColorProvider
     */
    public function testBackgroundColor(BackgroundColor $backgroundColors, string $expected)
    {
        $output = new TestStyle(new Output());

        $output->setBackground($backgroundColors);

        $output->write("Hello World");

        $this->assertEquals("\033[0;39;{$expected}mHello World", $output->getText());
    }

    public static function backgroundColorProvider(): iterable
    {
        yield [BackgroundColor::BLACK, BackgroundColor::BLACK->value];
        yield [BackgroundColor::RED, BackgroundColor::RED->value];
        yield [BackgroundColor::GREEN, BackgroundColor::GREEN->value];
        yield [BackgroundColor::YELLOW, BackgroundColor::YELLOW->value];
        yield [BackgroundColor::BLUE, BackgroundColor::BLUE->value];
        yield [BackgroundColor::MAGENTA, BackgroundColor::MAGENTA->value];
        yield [BackgroundColor::CYAN, BackgroundColor::CYAN->value];
        yield [BackgroundColor::WHITE, BackgroundColor::WHITE->value];
        yield [BackgroundColor::DEFAULT, BackgroundColor::DEFAULT->value];
    }

    /**
     * @dataProvider styleProvider
     */
    public function testTextStyle(AnsiStyle $textStyle, string $expected)
    {
        $output = new TestStyle(new Output());

        $output->setStyle($textStyle);

        $output->write("Hello World");

        $this->assertEquals("\033[{$expected};39;49mHello World", $output->getText());
    }

    public static function styleProvider(): iterable
    {
        yield [AnsiStyle::BOLD, AnsiStyle::BOLD->value];
        yield [AnsiStyle::DIM, AnsiStyle::DIM->value];
        yield [AnsiStyle::ITALIC, AnsiStyle::ITALIC->value];
        yield [AnsiStyle::UNDERLINED, AnsiStyle::UNDERLINED->value];
        yield [AnsiStyle::BLINK, AnsiStyle::BLINK->value];
        yield [AnsiStyle::REVERSE, AnsiStyle::REVERSE->value];
        yield [AnsiStyle::HIDDEN, AnsiStyle::HIDDEN->value];
        yield [AnsiStyle::RESET, AnsiStyle::RESET->value];
    }

    /**
     * @dataProvider colorWithBackgroundProvider
     */
    public function testColorWithBackground(TextColors $textColors, string $exceptedTextColor, BackgroundColor $backgroundColors, string $exceptedBackgroundColor)
    {
        $output = new TestStyle(new Output());

        $output->setColor($textColors)
            ->setBackground($backgroundColors);

        $output->write("Hello World");

        $this->assertEquals("\033[0;{$exceptedTextColor};{$exceptedBackgroundColor}mHello World", $output->getText());
    }

    public static function colorWithBackgroundProvider(): iterable
    {
        foreach (self::colorProvider() as $textColor) {
            foreach (self::backgroundColorProvider() as $backgroundColor) {
                yield [$textColor[0], $textColor[1], $backgroundColor[0], $backgroundColor[1]];
            }
        }
    }

    /**
     * @dataProvider colorWithStyleProvider
     */
    public function testColorWithStyle(AnsiStyle $textStyle, string $exceptedTextStyle, BackgroundColor $backgroundColors, string $exceptedBackgroundColor)
    {
        $output = new TestStyle(new Output());

        $output->setStyle($textStyle)
            ->setBackground($backgroundColors);

        $output->write("Hello World");

        $this->assertEquals("\033[{$exceptedTextStyle};39;{$exceptedBackgroundColor}mHello World", $output->getText());
    }

    public static function colorWithStyleProvider(): iterable
    {
        foreach (self::styleProvider() as $style) {
            foreach (self::backgroundColorProvider() as $backgroundColor) {
                yield [$style[0], $style[1], $backgroundColor[0], $backgroundColor[1]];
            }
        }
    }

    /**
     * @dataProvider StyleWithBackgroundProvider
     */
    public function testStyleWithBackground(TextColors $textColors, string $exceptedTextColor, AnsiStyle $textStyle, string $exceptedTextStyle)
    {
        $output = new TestStyle(new Output());

        $output->setColor($textColors)
            ->setStyle($textStyle);

        $output->write("Hello World");

        $this->assertEquals("\033[{$exceptedTextStyle};{$exceptedTextColor};49mHello World", $output->getText());
    }

    public static function StyleWithBackgroundProvider(): iterable
    {
        foreach (self::colorProvider() as $textColor) {
            foreach (self::styleProvider() as $style) {
                yield [$textColor[0], $textColor[1], $style[0], $style[1]];
            }
        }
    }

    /**
     * @dataProvider allModifierProvider
     */
    public function testWithAllModifier(TextColors $textColors, string $exceptedTextColor, BackgroundColor $backgroundColors, string $exceptedBackgroundColor, AnsiStyle $textStyle, string $exceptedTextStyle)
    {
        $output = new TestStyle(new Output());

        $output->setColor($textColors)
            ->setBackground($backgroundColors)
            ->setStyle($textStyle);

        $output->write("Hello World");

        $this->assertEquals("\033[{$exceptedTextStyle};{$exceptedTextColor};{$exceptedBackgroundColor}mHello World", $output->getText());
    }

    public static function allModifierProvider(): iterable
    {
        foreach (self::colorProvider() as $textColor) {
            foreach (self::backgroundColorProvider() as $backgroundColor) {
                foreach (self::styleProvider() as $style) {
                    yield [$textColor[0], $textColor[1], $backgroundColor[0], $backgroundColor[1], $style[0], $style[1]];
                }
            }
        }
    }

    public function testWithResetStyle()
    {
        $output = new TestStyle(new Output());

        $output->setColor(TextColors::CYAN)
            ->setBackground(BackgroundColor::GREEN)
            ->setStyle(AnsiStyle::BLINK);

        $output->setColor(TextColors::DEFAULT)
            ->setBackground(BackgroundColor::DEFAULT)
            ->setStyle(AnsiStyle::RESET);

        $output->write("Hello World");

        $this->assertEquals("\033[0;39;49mHello World", $output->getText());
    }
}

class Output extends ConsoleOutput
{
    public string $text = "";

    public function write(string $message, bool $newline = false): void
    {
        $this->text .= $newline ? PHP_EOL : '';
        $this->text .= $message;
    }
}

class TestStyle extends Style
{
    public function start(): void
    {
        $this->output->text .= sprintf("\033[%s;%s;%sm", $this->style->value, $this->color->value, $this->background->value);
    }

    public function getText(): string
    {
        return $this->output->text;
    }
}
