<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\VendorSpecific\PostgreSQL;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\Doctrine\AbstractType;
use Arokettu\IP\IPAddress;
use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv6Address;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class InetType extends AbstractType
{
    public const NAME = 'arokettu_postgres_inet';
    public const NATIVE_TYPE = 'inet';

    protected const CLASS_TITLE = 'IPAddress';
    protected const BASE_CLASSES = [
        IPv4Address::class,
        IPv6Address::class,
    ];

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv4Address || $address instanceof IPv6Address) {
            return $address->toString();
        }

        $this->throwInvalidArgumentException($address);
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPAddress::fromString($address);
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPAddress::fromString($address);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return self::NATIVE_TYPE;
    }
}
