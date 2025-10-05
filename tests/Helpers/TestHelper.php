<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests\Helpers;

final class TestHelper
{
    /**
     * @return resource<'stream'>
     */
    public static function stringToStream(string $s)
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $s);
        fseek($stream, 0);
        return $stream;
    }
}
