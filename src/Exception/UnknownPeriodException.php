<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class UnknownPeriodException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Unknown period.');
    }//end __construct()
}//end class
