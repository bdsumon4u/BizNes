<?php

declare(strict_types=1);

namespace App\Enum\Dimension;

use App\Traits\ArrayableEnum;
use App\Traits\HasEnumStaticMethods;

/**
 * @method static string KG()
 * @method static string G()
 * @method static string LBS()
 */
enum Weight: string
{
    use ArrayableEnum;
    use HasEnumStaticMethods;

    case KG = 'kg';

    case G = 'g';

    case LBS = 'lbs';
}
