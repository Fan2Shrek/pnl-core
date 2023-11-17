<?php

namespace Pnl\Console\Input;

enum ArgumentType: string
{
    case STRING = 'string';

    case INT = 'int';

    case BOOLEAN = 'bool';

    case FLOAT = 'float';
}
