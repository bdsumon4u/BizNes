<?php

namespace App\Enum\Dimension;

use App\Traits\ArrayableEnum;
use App\Traits\HasEnumStaticMethods;

/**
 * @method static string M()
 * @method static string CM()
 * @method static string MM()
 * @method static string FT()
 * @method static string IN()
 */
enum Length: string
{
    use ArrayableEnum;
    use HasEnumStaticMethods;

    case M = 'm';

    case CM = 'cm';

    case MM = 'mm';

    case FT = 'ft';

    case IN = 'in';
}
