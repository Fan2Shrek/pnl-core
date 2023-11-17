<?php

namespace Pnl\Console\Output\ANSI;

enum BackgroundColor: string
{
    case BLACK = '40';

    case RED = '41';

    case GREEN = '42';

    case YELLOW = '43';

    case BLUE = '44';

    case MAGENTA = '45';

    case CYAN = '46';

    case WHITE = '47';

    case DEFAULT = '49';

    case RESET = '0';
}
