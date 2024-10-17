<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\IPv4Address;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use InvalidArgumentException;

final class IPv4AddressType extends AbstractType
{
    protected const BASE_CLASSES = [
        IPv4Address::class,
    ];

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv4Address) {
            return $address->toString();
        }

        throw new InvalidArgumentException();
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
        $column['length'] = Values::IPV4_LENGTH;
        return $platform->getStringTypeDeclarationSQL($column);
    }
}
