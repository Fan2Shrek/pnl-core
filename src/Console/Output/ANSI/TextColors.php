<?php

namespace Pnl\Console\Output\ANSI;

enum TextColors: string
{
    case BLACK = '30';

    case RED = '31';

    case GREEN = '32';

    case YELLOW = '33';

    case BLUE = '34';

    case MAGENTA = '35';

    case CYAN = '36';

    case WHITE = '37';

    case DEFAULT = '39';

    case RESET = '0';

    public function start(): string
    {
        return sprintf("\033[%sm", $this->value);
    }
}
