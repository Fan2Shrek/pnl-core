<?php

namespace Pnl\Console\Output\ANSI;

enum Style: string
{
    case BOLD = '1';

    case DIM = '2';

    case ITALIC = '3';

    case UNDERLINED = '4';

    case BLINK = '5';

    case REVERSE = '7';

    case HIDDEN = '8';

    case RESET = '0';

    public function start(): string
    {
        return sprintf("\033[%sm", $this->value);
    }
}
