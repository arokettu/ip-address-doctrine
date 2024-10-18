<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\VendorSpecific\MariaDB;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\Doctrine\AbstractType;
use Arokettu\IP\IPv6Address;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class Inet6Type extends AbstractType
{
    public const NAME = 'arokettu_mariadb_inet6';
    public const NATIVE_TYPE = 'inet6';

    protected const CLASS_TITLE = 'IPv6Address';
    protected const BASE_CLASSES = [
        IPv6Address::class,
    ];

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv6Address) {
            return $address->toString();
        }

        $this->throwInvalidArgumentException($address);
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv6Address::fromString($address);
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv6Address::fromString($address);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return self::NATIVE_TYPE;
    }
}
