<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\VendorSpecific\MariaDB;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\Doctrine\AbstractType;
use Arokettu\IP\Doctrine\Values;
use Arokettu\IP\IPv4Address;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class Inet4Type extends AbstractType
{
    public const NAME = 'arokettu_mariadb_inet4';
    public const NATIVE_TYPE = 'inet4';

    protected const CLASS_TITLE = 'IPv4Address';
    protected const BASE_CLASSES = [
        IPv4Address::class,
    ];
    protected const LENGTH = Values::IPV4_LENGTH;

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv4Address) {
            return $address->toString();
        }

        $this->throwInvalidArgumentException($address);
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv4Address::fromString($address);
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv4Address::fromString($address);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return self::NATIVE_TYPE;
    }
}
