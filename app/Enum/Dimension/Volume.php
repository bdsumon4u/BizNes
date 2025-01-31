<?php

declare(strict_types=1);

namespace App\Enum\Dimension;

use App\Traits\ArrayableEnum;
use App\Traits\HasEnumStaticMethods;

/**
 * @method static string L()
 * @method static string ML()
 * @method static string GAL()
 * @method static string FLOZ()
 */
enum Volume: string
{
    use ArrayableEnum;
    use HasEnumStaticMethods;

    case L = 'l';

    case ML = 'ml';

    case GAL = 'gal';

    case FLOZ = 'floz';
}
